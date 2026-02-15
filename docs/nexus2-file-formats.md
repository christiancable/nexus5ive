# Nexus 2 File Format Reference

This document describes the binary and text file formats used by the original Nexus 2 BBS (a DOS-era C application). These formats are used by the legacy import tools in `app/Nexus2/`.

## Domain Model Mapping

| Nexus 2 | Nexus5ive | Description |
|---------|-----------|-------------|
| User | User | User account. Binary UDB file + text files (INFO.TXT, COMMENTS.TXT) |
| Menu | Section | Forum category. Text-based .MNU file with Lex/Yacc grammar |
| File | Topic | Discussion thread (article). Contains posts |

## Rights / Privilege Levels

Used across both UDB files (PrivLevel field) and MNU files (read/write fields):

| Value | Level | Constant |
|-------|-------|----------|
| 0 | Guest | USER_GUEST |
| 64 | Normal | USER_NORMAL |
| 100 | Default | DEFAULT_RIGHTS |
| 128 | Moderator | USER_MODERATOR |
| 255 | Sysop | USER_SYSOP |

## Original Source Code

The original C source is at `untracked/ucl_info/ADMIN/SOURCES/NEWBBS/WORKING/`:

| File | Purpose |
|------|---------|
| `UDBINDEX.H` | UDB struct definition (`_UDBStruct`) |
| `DEFINES.H` | Constants: FLAG_* indices, rights levels, privilege bits, ban bits |
| `MENU.H` | MENUSTRUCT definition |
| `MENUPARS.L` | Lex tokenizer for .MNU files (state machine) |
| `MENUPARS.Y` | Yacc grammar for .MNU files |
| `PARSCODE.C` | `FillStruct()` — populates MENUSTRUCT from parsed tokens |

---

## UDB (User Database)

**527 bytes** of fixed-width binary data per user, one file per user.

**Location**: `untracked/ucl_info/BBS/USR/{n}/NEXUS.UDB` (287 users, numbered directories)

### Struct Layout

From `_UDBStruct` in `UDBINDEX.H`:

| Offset | Size | Type | Field | Description |
|--------|------|------|-------|-------------|
| 0 | 17 | char[17] | Nick | Username/handle |
| 17 | 30 | char[30] | UserID | Institutional ID (e.g. "C.F.CABLE") |
| 47 | 30 | char[30] | RealName | Real name |
| 77 | 41 | char[41] | PopName | Tagline / status message |
| 118 | 1 | uchar | PrivLevel | Rights level (see table above) |
| 119 | 4 | ulong | TotalEdits | Post count (little-endian) |
| 123 | 4 | ulong | TimeOn | Total time online in minutes (little-endian) |
| 127 | 4 | ulong | TimesOn | Login count (little-endian) |
| 131 | 21 | char[21] | Password | Password hash (binary) |
| 152 | 30 | char[30] | Dept | Department |
| 182 | 30 | char[30] | Faculty | Faculty |
| 212 | 25 | char[25] | Created | Account creation date, e.g. "Tue 31/10/75 at 14:12:32" |
| 237 | 25 | char[25] | LastOn | Last login date, e.g. "Thu 3/6/99 at 17:27:05" |
| 262 | 13 | char[13] | HistoryFile | History filename, e.g. "HISTORY.0" |
| 275 | 2 | uint16 | UserNo | User number (little-endian, DOS uint = 16-bit) |
| 277 | 250 | uchar[250] | Flags | Flags area (see below) |

### String Field Handling

All `char[]` fields are **null-terminated C strings**. Bytes after the first `\0` are uninitialised garbage. When parsing, always truncate at the first null byte before trimming whitespace.

### PHP unpack() Format String

```php
'A17Nick/A30UserID/A30RealName/A41PopName/CRights/VTotalEdits/VTimeOn/VTimesOn/A21Password/A30Dept/A30Faculty/A25Created/A25LastOn/A13HistoryFile/vUserNo/C3Flags/CPriv/vBan/vBanned/CMaxLogins'
```

Format codes: `A` = space-padded string, `C` = unsigned char, `V` = uint32 LE, `v` = uint16 LE

### Flags Area (offset 277, 250 bytes)

The 250-byte area at the end of the struct is accessed as a flat byte array using `FLAG_*` constants from `DEFINES.H`. The C struct overlays named fields on the first bytes:

| Byte Index | C Struct Field | FLAG_* Constant | Description |
|------------|---------------|-----------------|-------------|
| 0 | Flags[0] | — | Unused |
| 1 | Flags[1] | FLAG_CONNO = 1 | Connection number |
| 2 | Flags[2] | FLAG_USERMODE = 2 | User mode |
| 3 | Priv (UDBPUnion) | FLAG_PRIVS = 3 | Privilege bitfield (see below) |
| 4 | Ban byte 1 | FLAG_BANPRIV = 4 | Ban privileges byte 1 |
| 5 | Ban byte 2 | FLAG_BANPRIV2 = 5 | Ban privileges byte 2 |
| 6 | Banned byte 1 | FLAG_BANNED = 6 | Banned-from byte 1 |
| 7 | Banned byte 2 | FLAG_BANNED2 = 7 | Banned-from byte 2 |
| 8 | MaxLogins | FLAG_MAXLOGIN = 8 | Max concurrent logins |
| 9 | — | FLAG_HIDE = 9 | Hide level |
| 10 | — | FLAG_SEX = 10 | Gender |
| 11 | — | FLAG_TIMEMODE = 11 | Time display mode |
| 12 | — | FLAG_CHAT = 12 | Chat permission |
| 13 | — | FLAG_MESSAGE = 13 | Message permission |
| 14 | — | FLAG_COMMENT = 14 | Comment permission |
| 15 | — | FLAG_MAIL = 15 | Mail permission |
| 16 | — | FLAG_NEWSTUFF = 16 | New stuff indicator |
| 17 | — | FLAG_UPDATE = 17 | Update indicator |
| 18 | — | FLAG_LOCKDELAY = 18 | Lock delay |
| 19 | — | FLAG_VALIDATED = 19 | Account validated |
| 20 | — | FLAG_SEEALL = 20 | Can see all items |

> **Important**: The `FLAG_*` constants are **0-indexed** C array indices. When using them in PHP to index into the unpacked flags array, use them directly — do NOT subtract 1.

### Flag Value Enums

**Sex** (FLAG_SEX): `0` = Unknown, `1` = Male, `2` = Female

**Hide** (FLAG_HIDE): `0` = All (invisible), `1` = Some, `2` = None (visible)

**Time Mode** (FLAG_TIMEMODE): `0` = 12h, `1` = 24h, `2` = None, `3` = 12h + Beep, `4` = 24h + Beep

**Enabled/Disabled** (FLAG_CHAT, FLAG_MESSAGE, FLAG_COMMENT, FLAG_MAIL): `0` = Enabled, `1` = Disabled, `2` = Banned

### Privilege Bitfield (byte 3 / FLAG_PRIVS)

| Bit | Value | Privilege |
|-----|-------|-----------|
| 0 | 1 | Expel users |
| 1 | 2 | Star Send (send to all) |
| 2 | 4 | Down BBS (shut down) |
| 3 | 8 | View Logs |
| 4 | 16 | Change Accounts |
| 5 | 32 | View Accounts |
| 6 | 64 | Anonymous View |
| 7 | 128 | Sysop |

### Ban Bitfields (bytes 4-7)

**Ban byte 1** (FLAG_BANPRIV) and **Banned byte 1** (FLAG_BANNED):

| Bit | Value | Capability |
|-----|-------|-----------|
| 0 | 1 | PopName |
| 1 | 2 | Mail |
| 2 | 4 | Print |
| 3 | 8 | Run |
| 4 | 16 | Send |
| 5 | 32 | Comment |
| 6 | 64 | Edit |
| 7 | 128 | Chat |

**Ban byte 2** (FLAG_BANPRIV2) and **Banned byte 2** (FLAG_BANNED2):

| Bit | Value | Capability |
|-----|-------|-----------|
| 0 | 1 | Talker |
| 1 | 2 | Use BBS |

### Companion Text Files

Located in the same directory as the UDB file (`untracked/ucl_info/BBS/USR/{n}/`):

| File | Description | Nexus5ive Equivalent |
|------|-------------|---------------------|
| `INFO.TXT` | User's profile/bio text | User info field |
| `COMMENTS.TXT` | Comments on the user's profile | Comment model |

`COMMENTS.TXT` stores lines in **reverse chronological order** (newest comment first in file). Each line is formatted as `{Nick} : comment text`.

---

## MNU (Menu) Files

**Text-based** menu definition files with a declarative grammar.

**Location**: `untracked/ucl_info/BBS/SECTIONS/{section}/{name}.MNU`

### Parser Architecture

The original C parser uses a **Lex/Yacc** (Flex/Bison) system:

1. **Lexer** (`MENUPARS.L`) tokenizes input using a state machine with states: INITIAL, NORMAL, COMMAND, COND, INIF, NAMELIST, INTITLE
2. **Parser** (`MENUPARS.Y`) applies grammar rules to determine field order and meaning
3. **Code generator** (`PARSCODE.C`) populates MENUSTRUCT arrays via `FillStruct()`

### How the Lexer Identifies Line Types

In the INITIAL state, the **first character** of a line determines its type. The lexer pattern `[Aa][^ \t]*[ \t]*` consumes the entire first word, so `a`, `art`, and `article` all match as ARTICLETYPE. Only the first letter matters, and matching is case-insensitive.

After matching the type prefix, the lexer enters NORMAL state where remaining tokens are classified:

| Pattern | Token Type | Examples |
|---------|-----------|----------|
| Single non-digit char | ACHAR | `o`, `*`, `b` |
| Multi-char word starting with non-digit | ASTRING | `whatson`, `*u`, `\sections\file.mnu` |
| Single digit | ANUMBER | `0`, `5` |
| Multi-digit number | AWORD | `100`, `180` |
| Number followed by non-whitespace | ASTRING | `16bit` |

Lines starting with `.` enter COMMAND state. Lines starting with `#`, `/`, or `;` are comments (ignored).

### Grammar Rules

From `MENUPARS.Y`, the exact field order for each line type:

```
ARTICLETYPE  read write key file flags info NEWLINE
FOLDERTYPE   read       key file flags info NEWLINE
COMMENTTYPE  read                      info NEWLINE
MCOMMENTTYPE read                      info NEWLINE
RUNTYPE      read       key file flags info NEWLINE
INTERNALTYPE read       key func flags info NEWLINE
HEADERTYPE   headername                     NEWLINE
```

Rules:
- **`flags`** is a **required** single token (ACHAR or ASTRING) for article, folder, run, and internal types
- **`info`** is **zero or more** remaining tokens, concatenated with spaces
- **Only articles have a `write` field** — folders, run, and internal types pass write=0
- **`key`** can be a letter (ACHAR) or a single digit (ANUMBER, converted to char via `'0' + digit`)

### Line Type Reference

| Prefix | Type | Enum Value | Nexus5ive | Fields |
|--------|------|------------|-----------|--------|
| `a` | Article | ARTICLE (2) | Topic | `read write key file flags [info...]` |
| `f` | Folder | MENU (1) | Child Section | `read key file flags [info...]` |
| `m` | Centred Comment | MCOMMENT (4) | (decoration) | `read [info...]` |
| `c` | Comment | COMMENT (3) | (decoration) | `read [info...]` |
| `i` | Internal | INTERNAL (0) | (built-in function) | `read key funcname flags [info...]` |
| `r` | Run | RUN (5) | (external program) | `read key file flags [info...]` |
| `H` | Header | HEADER (6) | Section title | `[headername...]` |
| `#` `/` `;` | Comment | — | — | Entire line ignored |

### Field Examples

**Article** — `a 0 100 o whatson * Whats On`
| Token | Field | Value | Meaning |
|-------|-------|-------|---------|
| `a` | type | article | Line type |
| `0` | read | 0 | Visible to all users |
| `100` | write | 100 | Normal users can post |
| `o` | key | o | Hotkey |
| `whatson` | file | whatson | Article data file |
| `*` | flags | * | Flags (single token) |
| `Whats On` | info | Whats On | Display text |

**Folder** — `f 0 b \sections\noticebd\aboutbbs\aboutbbs.mnu * {About the BBS}`
| Token | Field | Value | Meaning |
|-------|-------|-------|---------|
| `f` | type | folder | Line type |
| `0` | read | 0 | Visible to all |
| `b` | key | b | Hotkey |
| `\sections\...` | file | path to .mnu | Sub-menu file |
| `*` | flags | * | Flags |
| `{About the BBS}` | info | About the BBS | Display text (highlighted) |

**Comment** — `m 0 {General Noticeboard}`
| Token | Field | Value | Meaning |
|-------|-------|-------|---------|
| `m` | type | mcomment | Centred comment |
| `0` | read | 0 | Visible to all |
| `{General...}` | info | General Noticeboard | Display text |

### Dot Commands (Directives)

Lines starting with `.` are directives processed by the COMMAND lexer state:

| Command | Purpose | Syntax |
|---------|---------|--------|
| `.owner` | Set menu owners | `.owner nick1 nick2 nick3` |
| `.if` | Start conditional block | `.if condition` (see conditions below) |
| `.endif` | End conditional block | `.endif` |
| `.else` | Else clause | `.else` |
| `.noscan` / `.dontscan` | Mark subsequent items as unsubscribed | `.noscan` |
| `.scan` / `.doscan` | Mark subsequent items as subscribed (default) | `.scan` |
| `.include` | Include another menu file | `.include \path\to\file.mnu` |
| `.overlay` / `.use` | Overlay/merge another menu file | `.overlay \path\to\file.mnu` |
| `.quit` | Stop parsing menu | `.quit` (sysops with SEEALL bypass this) |
| `.pagebreak` | Pad with blank lines to fill page | `.pagebreak [privlevel]` (MENU_PAGE = 19) |
| `.title` | Set menu title file | `.title [random] [centre] filename` |
| `.flags` | Set default flags for subsequent items | `.flags flagstring` |
| `.repeat` | Repeat previous item | `.repeat N` |
| `.log` | Log item access | `.log filename` |

### Conditional Tests

The `.if` directive supports these conditions (from the COND and INIF lexer states):

| Condition | Syntax | Description |
|-----------|--------|-------------|
| User list | `.if user nick1 nick2 ...` | True if current user's nick is in the list |
| User in group | `.if userin groupname` | True if user belongs to named group |
| Sysop | `.if sysop` | True if user has PRIVS_SYSOP flag |
| Owner | `.if owner` | True if user is a menu owner (set by `.owner`) |
| Author | `.if author` | True if user is a BBS author (hardcoded: Valis, Chopper, Vordak, Flossy) |
| Privilege level | `.if privlevel relop number` | Compare user's rights level |
| BBS load | `.if load relop number` | Compare number of users currently online |
| Time | `.if time relop HH:MM[:SS]` | Compare current time |
| File exists | `.if exists filename` | True if file exists on disk |
| Day | `.if day dayname` | Check current day of week |
| Has privileges | `.if hasprivs privstring` | Check specific privilege flags |

**Privilege string characters** for `.if hasprivs`: `a` = Account, `d` = DownBBS, `f` = Anonymous, `l` = Logs, `s` = Sysop, `v` = View, `x` = Expel, `*` = DownBBS

**Relational operators**: `>`, `<`, `>=`, `<=`, `=`, `!=`

**Logical operators**: `&&` (AND), `||` (OR), `!` or `not` (NOT)

Conditional blocks can be nested.

### Display Text Highlighting

Nexus 2 uses inline markup for highlighted text. This appears in menu `info` fields, popnames, article subjects, and article bodies. The original implementation is `DisplayString()` in `UTILS.C`.

**Syntax:**
- `@x` — highlight a **single character**. The `@` is consumed and the immediately following character is displayed highlighted. E.g. `@Do@H` highlights "D" and "H" individually.
- `{text}` — highlight a **phrase**. Everything between `{` and `}` is displayed highlighted. E.g. `{About the BBS}` highlights the full phrase.

**Rules:**
- Highlighting is **per-line only**. An unclosed `{` is implicitly terminated at the end of the line and does not carry over to the next line.
- `@` at the very end of a string (with no following character) is consumed/ignored.
- `{` and `}` are markup characters and are removed from the displayed text.
- Parsers should return **raw text** with markup intact; highlight conversion happens at the display/rendering layer.

### FillStruct Behaviour

`FillStruct()` in `PARSCODE.C` populates the MENUSTRUCT and applies these rules:
- **Visibility**: Item is skipped if `read > UserInfo.Rights` (user's privilege is too low)
- **Default flags**: The `DefaultFlags` string (set by `.flags` directive) is appended to each item's flags field
- **Key uppercasing**: The hotkey character is uppercased via `toupper(key)`
- **Subscription**: The `Subbed` flag (toggled by `.scan`/`.noscan`) is stored in `Subscribed`
- **Sysop override**: `.quit` is bypassed if user has both PRIVS_SYSOP and FLAG_SEEALL; sysops with SEEALL can also see items inside false `.if` blocks (for ITEM type only, not CMD type)

### MENUSTRUCT (from MENU.H)

```c
typedef struct _MENUSTRUCT {
    unsigned int Type;          // enum elements value (0-7)
    unsigned int Read;          // minimum privilege to see (0-255)
    unsigned int Write;         // minimum privilege to post (0-255, articles only)
    char         Key;           // hotkey character (uppercased)
    char         File[MAXPATH]; // file path
    char         Flags[11];     // flag string (S_MENUFLAGS = 11)
    char         Owner[MAXPATH];// (unused in current code)
    char         Info[81];      // display text (S_MENUINFO = 81)
    char         Subscribed;    // 1 = subscribed, 0 = not (from .scan/.noscan)
} MENUSTRUCT;
```

---

## PHP Implementation

| Class | File | Purpose |
|-------|------|---------|
| `UdbParser` | `app/Nexus2/UdbParser.php` | Parses 527-byte UDB binary files |
| `MnuParser` | `app/Nexus2/MnuParser.php` | Parses text-based .MNU menu files |
| `ArticleParser` | `app/Nexus2/ArticleParser.php` | Parses article files (posts with ESC markers) |
| `NxText` | `app/Nexus2/NxText.php` | Highlight markup utility (`stripHighlights`, `toConsole`) |

### Preview Command

```bash
# Preview a user record
php artisan nexus2:preview udb path/to/NEXUS.UDB
php artisan nexus2:preview udb path/to/NEXUS.UDB --info --comments

# Preview a menu file
php artisan nexus2:preview mnu path/to/FILE.MNU

# Preview an article (topic with posts)
php artisan nexus2:preview article path/to/ARTICLEFILE

# Strip highlights for piping to a file
php artisan nexus2:preview article path/to/ARTICLEFILE --plain > output.txt
```
