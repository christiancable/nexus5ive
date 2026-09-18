# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Primary users are longtime BBS regulars — a small, persistent community who have used Nexus (nexus5.org.uk, in one form or another) since 2001, and who already know its culture, jargon, and social norms. Design work should assume returning, invested users rather than cold-acquisition visitors, and should not sand off in-jokes or terminology for the sake of a general audience.

## Product Purpose

Nexus5ive is a Laravel-built BBS (Bulletin Board System): sections (hierarchical forum categories) containing topics (threads) containing posts, plus profile comments, real-time chat, mentions, search, moderation reports, and an archive mode. Success is the community continuing to gather, post, and chat in a place that still feels like the BBS it always was.

## Positioning

It is Christian Cable's personal, long-running passion project and injoke — not a product competing with Discourse, phpBB, or Discord for market share. Its value is continuity and fun for its own small community, not competitive differentiation. Design and product decisions should optimize for what serves this specific community and its history, not for growth or general appeal.

## Operating Context

- Deployed at nexus5.org.uk, GPL v2 licensed, open to outside contributors (see CONTRIBUTING.md).
- Runs on Laravel Sail (Docker) for development; Laravel 12, PHP 8.2+, Livewire 3.5, Bootstrap 5.3, Vite.
- Core workflows: browsing sections → reading/posting in topics → replying to posts; profile comments; live chat with mentions; search; moderation via reports; archiving old sections/topics; per-user theme selection.
- Admins manage themes via `nexus:theme add/remove` (bundled Default and Excelsior themes, plus externally hosted Bootswatch themes); users pick from installed themes individually.

## Capabilities and Constraints

- **Theming**: Bootstrap-based, swappable per-user; supports both site-bundled themes and externally hosted CSS (e.g. Bootswatch). This mechanism must keep working — future visual work should extend or restyle within it, not replace it with a system that breaks user-selectable themes.
- **20+ years of continuity**: real user accounts, posts, and history dating to 2001 are a live asset. No future redesign should read as a relaunch that erases or disowns that history.
- **Open-source / GPLv2**: the codebase accepts outside pull requests; avoid design decisions that lock the project into proprietary or hard-to-contribute-to tooling.
- Registration can be toggled off (`NEXUS_ALLOW_REGISTRATIONS`); the site can run in `archive_mode` (read-only/frozen state) — both are real, supported states design must account for, not edge cases to ignore.

## Brand Commitments

- Name and identity: "Nexus 5ive" / "Nexus5ive", rendered in-app with a block ASCII-art wordmark (see readme.md).
- Site: nexus5.org.uk.
- Terminology inherited from BBS culture (e.g. sections, topics, "leap") should be preserved, not renamed to generic modern-forum language.

## Evidence on Hand

- Full working codebase at this repository, including live route map, models, and Livewire components (Chat, Mentions, Settings, Search, Userlist, PostCompose, ProfileMenu).
- readme.md documents install steps, theming, and development workflow.
- No user research, testimonials, analytics, or usage data on hand — none should be fabricated or implied in future design work.

## Product Principles

1. Preserve continuity: 20+ years of history and BBS-native terminology are assets, not legacy to modernize away.
2. Design for the regulars, not for cold acquisition: the audience already knows the culture; don't dilute it to read as generic.
3. Respect the theme system: any visual work must remain compatible with user-selectable, swappable themes (bundled and Bootswatch-style external).
4. Stay contributor-friendly: decisions should keep the codebase approachable for outside GPL contributors, not just the maintainer.
5. Treat it as a passion project, not a growth product: fun and fidelity to the community's identity outrank conversion or acquisition metrics.

## Accessibility & Inclusion

No product-specific accessibility requirement has been established beyond standard web accessibility practice.
