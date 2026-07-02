---
name: web-design
description: >-
  Superpower for designing beautiful, premium ("awwwards-level") web UI in this
  project. Use whenever building or redesigning any page, section, hero,
  component, card, landing page, or event template — or when the user asks to
  make something look better / more premium / less plain / more modern, or to
  improve visual hierarchy, typography, color, spacing, layout, depth, or
  motion. Grounded in the project's Vue 3 + Inertia + Tailwind stack and the
  custom `gl-*` animation toolkit in resources/css/app.css.
---

# Web design superpower

Your job is not "make it not broken." It's to make it look **intentional,
expensive, and alive** — the difference between a template and a designer's
portfolio piece. Amateurs reach for markup first; you decide the *vibe*, the
*type scale*, and the *one accent* first, then build.

Read `references/design-system.md` for the exact project utilities (the `gl-*`
toolkit, accent tokens, layouts, template architecture) — cite real class names,
never invent them.

## 0. Before writing any markup

1. **Name the vibe in 3 words.** e.g. "editorial, warm, calm" or "nocturnal,
   luxe, cinematic." Every later decision serves those words.
2. **Pick one reference feeling** (Joy/withjoy, Zola, Linear, Vercel, Awwwards
   winners). Steal structure, not pixels.
3. **Commit to a spatial system:** a max-width (`max-w-5xl`/`6xl`/`7xl`), a
   consistent section rhythm (`py-24`/`py-28` for big sections), and a type
   scale. Consistency reads as "designed."

## 1. The premium non-negotiables

These are what separate pro from plain. If a design "looks flat/plain," it is
almost always missing several of these:

- **One oversized moment.** A hero headline that is *genuinely* big
  (`gl-display`, clamp to ~9rem) with tight leading (`leading-[0.9]`) and
  negative tracking (`tracking-tight`). Timid 3xl headings read as amateur.
- **Restraint in color.** One accent, lots of neutral, real black or near-black
  for drama (`#0d0b14`, `#080611`). Never rainbow unless the vibe *is* festive.
- **Whitespace as a feature.** Double the padding you first reach for. Let
  things breathe; crowding is the #1 amateur tell.
- **Depth & texture.** Flat #fff on #fff is dead. Add film grain (`gl-grain`),
  an animated mesh gradient (`gl-mesh-dark`/`gl-mesh-warm` + `gl-mesh-anim`),
  soft layered shadows, or a subtle ring (`ring-1 ring-neutral-200/70`).
- **Motion that rewards scrolling.** Entrance on load (`gl-enter` + `gl-d1..d4`
  stagger), scroll reveals (`data-reveal`), one ambient loop (float, marquee,
  spin). Never everything at once.
- **A type pairing.** A display/serif for emotion + a clean sans for UI. Mixing
  weights (font-black next to font-light) creates hierarchy for free.
- **Detail at the edges.** Rounded corners that match (`rounded-2xl`+),
  hover states on everything interactive, a sheen on primary buttons
  (`gl-btn`), badges/eyebrows in uppercase micro-tracking
  (`text-xs uppercase tracking-[0.4em]`).

## 2. Typography

- **Scale, don't nudge.** Jump sizes (sm → lg → 3xl → display), don't use
  4 near-identical sizes. Big contrast between headline and body.
- **Eyebrows** above headlines: `text-xs font-semibold uppercase
  tracking-[0.35em] text-{accent}` — instantly premium.
- **Body** maxes out at `max-w-xl`/`prose` width and `leading-7`/`leading-8`.
  Long full-width lines are unreadable.
- **Numbers/data**: `tabular-nums`, oversized and light for schedules/countdowns
  (`text-5xl font-black` or `font-serif font-light`).
- Above-the-fold headings must **never** depend on a scroll observer to appear —
  use on-load animation (`gl-clip`, `gl-enter`), not `data-reveal`.

## 3. Color

- Build on **neutrals first** (`neutral-950 → neutral-50`), then add **one**
  accent from the token set (indigo/rose/amber/violet/emerald/sky/neutral).
- **Dark sections create luxury.** Alternate a light editorial section with a
  near-black one (mesh + grain) for rhythm.
- Use **opacity for hierarchy** on colored backgrounds (`text-white/70`,
  `border-white/10`, `bg-white/[0.04]`) instead of new colors.
- Gradients: keep them tonal (same family) or on-brand; animate slowly with
  `gl-pan`. Avoid muddy complementary gradients.
- **Always check contrast** (WCAG AA: 4.5:1 body, 3:1 large text).

## 4. Layout, grid & spacing

- **Asymmetry > centered-everything.** Editorial grids like
  `lg:grid-cols-[0.4fr_1fr]` (label / content) feel designed; stacked centered
  blocks feel like a form.
- **Break the grid** deliberately: an oversized number, a full-bleed image, a
  marquee band spanning edge-to-edge (`gl-marquee`), a rotated card.
- **Vertical rhythm:** pick section padding once (`py-24`+) and reuse. Separate
  sections with a hairline (`border-t border-neutral-200`) or a color flip, not
  random gaps.
- **Consistent radii & shadows** across a page — mismatched corner radii is a
  subtle amateur tell.
- Cards: give them a reason to feel tactile — `hover:-translate-y-1`,
  `transition`, image zoom (`group-hover:scale-105`), a ring or soft shadow.

## 5. Motion (use the `gl-*` toolkit — don't hand-roll keyframes)

- **On load:** `gl-enter`/`gl-enter-slow`/`gl-pop` with `gl-d1..gl-d4` to
  stagger. Hero name via `gl-clip` (wipe that always ends visible).
- **On scroll:** add `data-reveal` (or `="left"|"right"|"zoom"`) to
  below-the-fold blocks and call `useReveal()` once in the component.
- **Ambient (pick ≤1 per section):** `gl-float`, `gl-blob`, `gl-marquee`,
  `gl-spin-slow`, `gl-twinkle`, `gl-fall` (petals/confetti), `gl-pan` (bg).
- **Buttons:** `gl-btn` for the sheen + `hover:scale-105`.
- **Restraint:** motion should guide the eye, not fight it. If two things move
  at once and compete, kill one.
- The toolkit already respects `prefers-reduced-motion` — keep it that way; if
  you add a new animation class, add it to that media block in app.css.

## 6. This project's building blocks

See `references/design-system.md` for the full inventory. In short:
- **Global toolkit:** `resources/css/app.css` — the `gl-*` classes above.
- **Composables:** `resources/js/Templates/useReveal.js` (scroll reveal),
  `useEventDisplay.js` (event data: hosts, countdown, gallery, coverPhoto…).
- **Layouts:** `PublicLayout.vue` (liquid-glass fixed nav for guest pages),
  `AuthenticatedLayout.vue` (slate/indigo app shell + flash toast).
- **Templates:** `resources/js/Templates/Website/*` & `Invitation/*` are the
  reference for premium composition — study Modern/Midnight/Botanical before
  designing a new immersive page.
- **Fonts:** Figtree (sans, default). Use `font-serif` for elegant/editorial.

## 7. Pre-ship QA checklist

Before calling a design done, verify:
- [ ] **Responsive:** looks intentional at 375px, 768px, 1440px. No horizontal
      scroll. Display type uses `clamp`/responsive sizes, not fixed huge px.
- [ ] **Hierarchy:** squint test — the one most important thing is obviously
      biggest/boldest.
- [ ] **Above-the-fold content is visible without scrolling** (no reveal-only
      headings that vanish if the observer misfires).
- [ ] **Contrast** passes AA for text over images/gradients (add an overlay
      like `bg-black/40` when text sits on a photo).
- [ ] **Reduced motion:** with the OS setting on, nothing is invisible or
      janky.
- [ ] **Interactive affordance:** every button/link has hover + focus states.
- [ ] **Consistency:** radii, shadow depth, section padding, and accent are
      uniform across the page.
- [ ] **Build passes** (`npm run build`) and the page renders (drive it, don't
      assume — see the `verify`/`run` skills).

## 8. Anti-patterns (instant "plain/amateur" signals)

- Timid headlines; everything the same size.
- Pure #fff on #fff with no texture, depth, or dark section.
- Everything centered and stacked; no asymmetry, no full-bleed moment.
- More than one accent color fighting for attention.
- Cramped spacing / walls of full-width text.
- Motion on everything, or motion that blocks reading content.
- Inventing CSS classes instead of using the `gl-*` toolkit / Tailwind tokens.
- Reusing a stock Bootstrap-ish card grid with flat borders and no hover.
