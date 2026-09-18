---
name: Nexus 5ive — Default Theme
description: A calm, Bootstrap-native blue-on-mist skin for a longtime BBS community, built so the optional seasonal/novelty themes are what actually feels loud.
colors:
  primary: "#2679a7"
  secondary: "#8eb4cb"
  success: "#20865c"
  info: "#616fc5"
  warning: "#f6993f"
  danger: "#bf5329"
  neutral-bg: "#f5f8fa"
  neutral-text: "#636b6f"
  neutral-white: "#ffffff"
  neutral-black: "#000000"
typography:
  display:
    fontFamily: "Nunito, system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif"
    fontSize: "3.5rem"
    fontWeight: 300
    lineHeight: 1.2
  headline:
    fontFamily: "Nunito, system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif"
    fontSize: "2.25rem"
    fontWeight: 600
    lineHeight: 1.6
  title:
    fontFamily: "Nunito, system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif"
    fontSize: "1.35rem"
    fontWeight: 600
    lineHeight: 1.6
  body:
    fontFamily: "Nunito, system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif"
    fontSize: "0.9rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Nunito, system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif"
    fontSize: "0.7875rem"
    fontWeight: 400
    lineHeight: 1.6
rounded:
  sm: "0.375rem"
  md: "0.5rem"
  pill: "50px"
spacing:
  sm: "0.75rem"
  md: "1rem"
  lg: "1.5rem"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.neutral-white}"
    rounded: "{rounded.sm}"
    padding: "0.375rem 0.75rem"
  button-secondary:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.neutral-black}"
    rounded: "{rounded.sm}"
    padding: "0.375rem 0.75rem"
  card:
    backgroundColor: "{colors.neutral-white}"
    textColor: "{colors.neutral-text}"
    rounded: "{rounded.sm}"
    padding: "1rem"
---

# Design System: Nexus 5ive — Default Theme

## Overview

**Creative North Star: "The Calm Common Room"**

The Default theme is the room everyone starts in: friendly, unpretentious, and deliberately quiet, built entirely on stock Bootstrap 5.3 behavior with a light coat of Nunito and Nexus Sky Blue. It exists so that the site's optional theme packs — Excelsior's black-and-neon terminal look, Halloween, Hotdog, Xmas, the legacy Nexus2 skin — are the ones that get to be visually loud. The Default theme's job is to stay out of the way: legible, warm, a little plain by design, and explicitly not sleek corporate SaaS.

Components come across as tidy and a little playful rather than austere: standard Bootstrap bones (navbar, cards, breadcrumbs, buttons) carry a few hand-placed touches — pill-shaped chat bubbles, icon-accented nav links, a quote-mark blockquote treatment — that give the app personality without asking it to look bolder than it is. This is a passion project for a small, invested community; the visual system should feel sturdy and familiar to a returning user, never like a relaunch.

**Key Characteristics:**
- Cool, mist-and-slate neutral canvas with one confident accent (Nexus Sky Blue) doing most of the color work.
- Stock Bootstrap grid, spacing, and component shapes — no attempt to hide or restyle the framework.
- Flat by default; the rare soft shadow marks a quote or a message bubble, never generic "raised UI."
- Personality lives in small, specific details (icon sizing, chat bubbles, blockquote quote-mark), not in a system-wide visual statement.
- The Default theme is one of several installable Bootstrap-variable themes; nothing here should assume it's the only skin the app will ever wear.

## Colors

Cool Mist and Slate: a very light cool-gray page background paired with muted slate-gray body text, built for long, easy reading sessions — then one confident accent blue doing nearly all the "this is interactive" signaling.

### Primary
- **Nexus Sky Blue** (#2679a7): the site's signature color — primary buttons, links, the top toolbar and mobile bottom-nav background, active/selected states. Used generously; it *is* the site's color identity, not a rare accent. Deepened from an earlier, brighter #3097D1 so white button/nav text and link text on the page background both clear WCAG AA (4.5:1) — same hue and character, just legible.

### Secondary
- **Soft Cyan** (#8eb4cb): secondary buttons and lower-emphasis actions. Paired with black text (`$btn-secondary-color: $black`) for contrast, since it's too light to carry white text reliably.

### Tertiary (contextual accents)
- **Leaf Green** (#20865c) — success states, confirmations. Deepened from #2ab27b for the same AA reason as Nexus Sky Blue.
- **Indigo Accent** (#616fc5) — informational states. Deepened from #6574cd for the same AA reason.
- **Amber Orange** (#f6993f) — warnings; paired with black text for contrast.
- **Brick Red** (#bf5329) — destructive actions, errors, delete/report flows.

### Neutral
- **Cool Mist** (#f5f8fa): page background.
- **Slate Ink** (#636b6f): body text color.
- **Neutral White** (#ffffff) / **Neutral Black** (#000000): text-on-color pairing per button/badge variant (see Named Rule below).

### Named Rules
**The Readable-Text Rule.** Every theme color has an explicit, hand-picked text color (`$btn-primary-color`, `$btn-secondary-color`, etc.) rather than relying on Bootstrap's automatic contrast calculation — primary/success/info/danger/dark get white text, secondary/warning/light get black text. This exists specifically to fix contrast issues Bootstrap's defaults didn't handle well; don't remove the explicit overrides in favor of automatic contrast.

**The One Signature Blue Rule.** Nexus Sky Blue is the only color that should read as "the site's color" — new UI should reach for `$primary` before introducing a new hue, matching how the toolbar, bottom nav, links, and primary actions already share it.

**The AA-Or-Darker Rule.** `$primary`, `$success`, and `$info` are calibrated so their white-text pairing (and, for `$primary`, its use as link text on `$neutral-bg`) clears 4.5:1. If a future rebrand changes these hues, re-check contrast against both white and `#f5f8fa` before shipping — don't reintroduce a brighter shade that looks nicer but fails AA again.

## Typography

**Display Font:** Nunito (with system-ui, -apple-system, "Segoe UI", Roboto fallback)
**Body Font:** Nunito (same family — the system uses one typeface throughout, differentiated by weight, not by pairing two fonts)

**Character:** A single rounded, humanist sans-serif carries the whole system. Headings step up to semi-bold (600) for structure; body text stays at regular weight for long-form reading; one oversized, light-weight (300) display cut is reserved for page-level hero headings.

### Hierarchy
- **Display** (300 weight, 3.5rem, 1.2 line-height): Bootstrap's `.display-4` utility, used directly by the page-heading component (`<x-heading>`) for section/topic/page titles — deliberately lighter and larger than the semantic `<h1>` styling below it.
- **Headline** (600 weight, 2.25rem / `$h1-font-size`, 1.6 line-height): semantic `<h1>` where used outside the heading component.
- **Title** (600 weight, 1.35rem–1.575rem / `$h3`–`$h4`, 1.6 line-height): card titles, topic headings, section titles.
- **Body** (400 weight, 0.9rem base, 1.6 line-height): the base font size is intentionally smaller and looser than Bootstrap's default (1rem / 1.5), trading a touch of density for easier long-session reading. Post and comment content should not exceed a comfortable measure inside the standard Bootstrap container.
- **Label** (400 weight, ~0.7875rem / Bootstrap's `small`, 1.6 line-height): timestamps, muted footer text, breadcrumb items.

### Named Rules
**The Single-Typeface Rule.** Don't introduce a second font family for emphasis or "distinction" — weight and size carry all the hierarchy this system needs. A new display treatment should still resolve to Nunito.

## Layout

Standard Bootstrap 5.3 container/grid system, unmodified — `.container`, responsive breakpoints at Bootstrap's defaults, `lg` as the collapse point for the primary navbar. Two structural patterns are specific to Nexus:

- **Dual navigation for authenticated users**: a full top toolbar (`_toolbar.blade.php`) at all sizes, plus a second `fixed-bottom d-lg-none` navigation bar that appears only below the `lg` breakpoint — a mobile-app-style bottom tab bar layered on top of an otherwise ordinary Bootstrap page. A `.footer-navigation-spacer` (4rem on small screens, 0 at `lg`+) reserves space at the bottom of the content so the fixed bar never covers content.
- **Breadcrumbs as a page-identity strip**: every content page renders a `.container` breadcrumb trail directly under the toolbar, before the alerts and page content — it's load-bearing navigation for a deeply nested section/topic hierarchy, not decoration.

Collapse animation (Bootstrap's accordion/navbar collapse) is sped up from Bootstrap's default 0.35s to 0.15s ease — the system favors snappy, immediate feedback over lingering transitions.

## Elevation & Depth

Flat by default. Cards, the navbar, and breadcrumbs use unmodified Bootstrap surfaces with no added shadow — depth is not a general design signal in this system. The two deliberate exceptions both mark "this is a quoted/conversational unit," not "this is raised UI":

### Shadow Vocabulary
- **Quote callout** (`box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1)`): used only on `<blockquote>`, alongside a large translucent quotation-mark glyph and a tinted background — marks quoted content specifically.
- (Chat bubbles carry no shadow; their distinction comes from shape and color, not elevation — see Components.)

### Named Rules
**The Flat-Unless-Quoted Rule.** Don't add a shadow to a card, button, or nav element to make it feel "important." Shadow is reserved for content that is literally being quoted back to the reader.

## Shapes

Bootstrap's default corner radius (0.375rem) is unmodified and governs buttons, cards, and inputs system-wide — nothing in the Default theme sharpens or rounds this further (contrast this with the Excelsior theme, which overrides `$border-radius` to 0.8rem and `$border-width` to 5px; theme packs are explicitly allowed to change this, the Default theme is not obligated to anticipate them).

The blockquote callout uses one step up (0.5rem, `rounded.md`) — shared across every theme, unlike the per-theme radius overrides, so it belongs in the documented scale rather than reading as drift.

The one deliberate departure is the chat bubble: a heavily rounded pill (`border-radius: 50px`) with one corner squared off (`border-bottom-right-radius: 0` for sent messages, `border-bottom-left-radius: 0` for received) — a speech-bubble silhouette distinct from every other rounded-rectangle surface in the system.

The blockquote's oversized quotation-mark glyph (3rem, stepping to 2rem on narrow screens) is an intentional one-off decorative size, not a type-ramp step — it exists once, for one purpose, and shouldn't be promoted into the typography scale.

## Components

### Buttons
- **Shape:** Bootstrap default radius (0.375rem), no override.
- **Primary:** Nexus Sky Blue background, white text, `0.375rem 0.75rem` padding at base size (most in-app usage renders at `btn-sm` via the `<x-ui.button>` component's default size prop).
- **Secondary:** Soft Cyan background, **black** text (explicit override — see Readable-Text Rule).
- **Hover / Focus:** Bootstrap default state treatment; no custom hover animation.
- **Icon slots:** `<x-ui.button>` and `<x-ui.button-link>` both accept optional `icon` / `iconRight` slots rendered inline with `me-1` / `ms-1` spacing — buttons routinely pair a Heroicon with their label rather than standing as text-only.

### Cards
- **Corner Style:** Bootstrap default radius, unmodified.
- **Background:** White by default; the user-card variant uses `bg-light`.
- **Shadow Strategy:** None (see Elevation & Depth) — cards are flat.
- **Header pattern:** Post cards use a colored `card-header` (`bg-primary text-white`) only when the post has a title; titleless posts skip the header entirely rather than rendering an empty one.
- **Internal Padding:** Bootstrap default card-body padding.

### Chat Bubbles (signature component)
- **Shape:** 50px pill radius with one flat corner (see Shapes) — sent and received messages are visually distinct by both color and which corner is squared.
- **Sent:** background `$success` (Leaf Green, #20865c in the Default theme), white text, bottom-right corner squared.
- **Received:** background `$primary` (Nexus Sky Blue, #2679a7 in the Default theme), white text, bottom-left corner squared.
- Bubble colors are theme variables, not literals — they recolor automatically with Excelsior, Halloween, Hotdog, Xmas, and Nexus2.

### Navigation
- **Top toolbar:** `navbar-dark bg-primary`, so every link/icon in it renders white-on-blue; collapses below `lg` into a hamburger toggle that also carries an unread-notification badge (`text-bg-danger`) when the collapsed state hides pending notifications.
- **Bottom tab bar (mobile only):** `fixed-bottom d-lg-none bg-primary`, white icon+label links in an even-width row — only rendered for authenticated users, only below `lg`.
- **Breadcrumbs:** Bootstrap default `.breadcrumb` styling, no custom color treatment currently applied (a commented-out tinted-background variant exists in the partial but is not active).

### Icons
- Heroicons (solid set) via Blade components, sized through three fixed utility classes rather than Bootstrap's own scale: `.icon_mini` (1rem), `.icon_medium` (2rem), `.icon_large` (3rem). New icon usage should reach for one of these three, not an ad hoc inline size.

## Do's and Don'ts

### Do:
- **Do** keep every new color decision inside the existing `$theme-colors` map (primary/secondary/success/info/warning/danger/light/dark) so it survives a theme switch to Excelsior, Halloween, Hotdog, Xmas, or Nexus2 without extra work.
- **Do** give every new colored button/badge variant an explicit text-color override (see Readable-Text Rule) rather than trusting automatic contrast.
- **Do** use the three fixed icon-size classes (`icon_mini` / `icon_medium` / `icon_large`) for any new Heroicon usage.
- **Do** treat shadow as a signal reserved for quoted/conversational content, not general UI weight.

### Don't:
- **Don't** hardcode a new hex color for chat, badges, or alerts when a `$theme-colors` entry already covers the intent — this drifted once (chat bubbles used raw Bootstrap defaults instead of `$success`/`$primary`) and was fixed by switching to the theme variables; don't reintroduce a literal.
- **Don't** introduce a second typeface. Nunito plus weight variation is the entire type system.
- **Don't** add drop shadows to cards, nav, or buttons to signal elevation or importance — this system is flat by default.
- **Don't** assume the Default theme's exact colors, radius, or font are load-bearing outside it — Excelsior, Halloween, Hotdog, Xmas, and Nexus2 each override these Bootstrap variables wholesale, and that override mechanism is a first-class product feature, not a shortcut to work around.
