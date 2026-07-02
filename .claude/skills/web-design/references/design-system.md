# Gift Loft design-system inventory

Concrete, real class names and files to cite when designing. Source of truth is
`resources/css/app.css` — if you add a utility, add it there **and** to the
`prefers-reduced-motion` block. Verify against the file before asserting; this
inventory can drift.

## The `gl-*` toolkit (resources/css/app.css)

### Entrance (play on mount)
| Class | Effect |
|---|---|
| `gl-enter` | fade + rise in (0.9s) |
| `gl-enter-slow` | same, slower (1.2s) |
| `gl-fade` | fade only |
| `gl-pop` | scale-in with slight overshoot |
| `gl-d1` `gl-d2` `gl-d3` `gl-d4` | stagger delays (0.15–0.6s) — add alongside an entrance class |
| `gl-clip` | clip-path wipe-up for headings; **always ends visible** (use for hero names, not `data-reveal`) |

### Scroll reveal (needs `useReveal()` in the component)
- `data-reveal` on an element → fades/rises in when scrolled into view.
- Variants: `data-reveal="left"`, `="right"`, `="zoom"`.
- Only for **below-the-fold** content.

### Ambient loops (use ≤1 per section)
`gl-float`, `gl-float-soft` (gentle bob), `gl-blob` (morphing blob radius),
`gl-fall` (falling petals/confetti — set `left`, `animation-delay`,
`animation-duration` inline), `gl-twinkle` (sparkle), `gl-pulse` (soft pulse),
`gl-spin-slow` (24s rotation), `gl-grow` (underline/divider scaleX).

### Premium finishers
| Class | Use |
|---|---|
| `gl-grain` | film-grain overlay — add to a `relative` container for texture |
| `gl-mesh-dark` / `gl-mesh-warm` | animated gradient mesh background (pair with `gl-mesh-anim`) |
| `gl-mesh-anim` | animates the mesh position |
| `gl-pan` | slow-panning gradient background (`background-size:200%`) |
| `gl-marquee` | edge-to-edge scrolling band; modifiers `gl-marquee-fast`, `gl-marquee-rev` |
| `gl-display` | oversized clamp headline (~2.75→9rem, leading 0.92) |
| `gl-display-sm` | smaller oversized headline (~2.25→5rem) |
| `gl-outline` | stroked/outline text (transparent fill) |
| `gl-btn` | primary-button sheen sweep on hover |
| `gl-shimmer` | shimmer sweep across text (gradient-clip) |

All of the above are switched off under `prefers-reduced-motion` — keep new
animations in that block.

## Accent tokens

Accent keys used across the app (`Events/Design.vue` `accentBg`,
`Public/Shop.vue` / `ProductDetail.vue` `accentGrad`):
`indigo`, `neutral`, `rose`, `amber`, `violet`, `emerald`, `sky`.

- Solid bg map lives in `Design.vue` (`accentBg`).
- Gradient map lives in `Shop.vue`/`ProductDetail.vue` (`accentGrad`, e.g.
  `indigo → 'from-indigo-500 to-violet-500'`).
Pick **one** accent per surface; use neutrals + opacity for everything else.

## Dark palette anchors (from templates)
`#0d0b14`, `#080611`, `#0e0a1f`, `#140a2e` (near-black canvases);
warm light canvases `#f4f3f0`, `#fbf6ec`, `#fff7f3`, `#f3f4ee`.

## Layouts
- `resources/js/Layouts/PublicLayout.vue` — guest/marketing pages. Liquid-glass
  **fixed** nav (`fixed inset-x-0 top-0` + backdrop-blur) that sits over a dark
  hero. No flash toast — confirm actions with in-page state.
- `resources/js/Layouts/AuthenticatedLayout.vue` — logged-in app shell,
  slate/indigo, shows `flash.success`/`flash.error` toast.

### Futuristic finishers
`gl-aurora` (drifting blurred color field), `gl-ken` (Ken Burns photo drift),
`gl-border-glow` (spinning conic border; set `--gl-bg`), `gl-glow` (neon pulse;
set `--gl-glow`), `gl-tilt` (3D hover), `gl-gridlines` (edge-faded grid),
`gl-photo` (slow zoom frame). Don't combine gsap transforms with `gl-*`
transform animations on the same element — use a wrapper.

## Composables
- `resources/js/Templates/useReveal.js` — IntersectionObserver; call once,
  activates all `data-reveal` in the component.
- `resources/js/Templates/useScrollFx.js` — **GSAP ScrollTrigger engine**,
  attribute-driven: `data-fx="parallax|hero-exit|scale-in|chars|rotate|draw|
  hscroll|batch"` (+ `data-speed`, `data-turns`, `data-fx-track`). Call
  `useScrollFx(rootRef)` with a ref on the component root; cleanup + reduced
  motion handled. hscroll pins the section and scrolls a horizontal track.
- `resources/js/Templates/useEventDisplay.js` — normalized event display data:
  `typeLabel, hosts, tagline, dressCode, rsvpNote, schedule, faqs, location,
  venueNote, travel, stay, mapUrl, gallery, coverPhoto, registryUrl, countdown,
  fmtFull, fmtDate`.

## Reference implementations (study before building new immersive UI)
- **Website templates:** `resources/js/Templates/Website/{Classic, Modern,
  Festive, Botanical, Midnight, Storybook}.vue` — full-screen heroes, marquee
  bands, numbered schedules, scroll-revealed sections, themed galleries.
- **Invitation cards:** `resources/js/Templates/Invitation/{Elegant, Floral,
  Bold, Minimalist, Deco, Confetti}.vue`.
- **Registry/shop UI:** `Public/Shop.vue`, `Public/ProductDetail.vue`,
  `Public/WishlistShow.vue`.

Best exemplars of each move: **Modern** (editorial/brutalist asymmetry +
marquee), **Midnight** (luxe dark + grain + starfield), **Botanical**
(soft light editorial), **Festive** (colorful mesh + confetti — the one time
multi-color is right).

## Stack notes
- Vue 3 `<script setup>` + Inertia; Tailwind (Figtree sans via
  `tailwind.config.js`, default serif stack for `font-serif`); `@tailwindcss/forms`.
- Build: `npm run build` (Vite). Dev: `npm run dev` + `php artisan serve`.
