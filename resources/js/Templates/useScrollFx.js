import { onMounted, onUnmounted } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/**
 * GSAP ScrollTrigger effects, driven by data attributes so every template
 * shares one engine. Call once per template component:
 *
 *   const root = ref(null);          // on the template's root element
 *   useScrollFx(root);
 *
 * Supported attributes:
 *   data-fx="parallax"  data-speed="0.25"  — scrubbed vertical drift (± speed×220px)
 *   data-fx="hero-exit"                    — fades/lifts out as its <section> scrolls away
 *   data-fx="scale-in"                     — scrubbed scale 0.85→1 + fade as it enters
 *   data-fx="chars"                        — splits text into chars, staggered rise on enter
 *   data-fx="rotate"    data-turns="0.5"   — rotation scrubbed across the viewport pass
 *   data-fx="draw"                         — SVG path stroke draws itself on scrub (put on <svg>)
 *   data-fx="hscroll"                      — pinned section; child [data-fx-track] scrolls horizontally
 *   data-fx="batch"                        — children fade-rise in with stagger when entering
 *
 * Cleanup is automatic (gsap.context). Reduced-motion users get static layouts.
 */
export function useScrollFx(rootRef) {
    let ctx;

    onMounted(() => {
        const root = rootRef?.value;
        if (!root) return;

        ctx = gsap.context(() => {
            const mm = gsap.matchMedia();

            mm.add('(prefers-reduced-motion: no-preference)', () => {
                // ── Parallax drift ──
                root.querySelectorAll('[data-fx="parallax"]').forEach((el) => {
                    const speed = parseFloat(el.dataset.speed ?? '0.25');
                    gsap.fromTo(el, { y: speed * 220 }, {
                        y: speed * -220,
                        ease: 'none',
                        scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
                    });
                });

                // ── Hero content lifts away as you leave the hero ──
                root.querySelectorAll('[data-fx="hero-exit"]').forEach((el) => {
                    const section = el.closest('section') ?? el;
                    gsap.to(el, {
                        opacity: 0,
                        y: -90,
                        ease: 'none',
                        scrollTrigger: { trigger: section, start: 'top top', end: '75% top', scrub: true },
                    });
                });

                // ── Scrubbed scale-in ──
                root.querySelectorAll('[data-fx="scale-in"]').forEach((el) => {
                    gsap.fromTo(el, { scale: 0.85, opacity: 0.35 }, {
                        scale: 1,
                        opacity: 1,
                        ease: 'none',
                        scrollTrigger: { trigger: el, start: 'top 96%', end: 'top 55%', scrub: true },
                    });
                });

                // ── Character split reveal ──
                root.querySelectorAll('[data-fx="chars"]').forEach((el) => {
                    if (el.dataset.fxSplit) return;
                    el.dataset.fxSplit = '1';
                    const chars = [...el.textContent].map((c) => {
                        const s = document.createElement('span');
                        s.textContent = c;
                        s.style.display = 'inline-block';
                        s.style.whiteSpace = c === ' ' ? 'pre' : 'normal';
                        return s;
                    });
                    el.textContent = '';
                    el.append(...chars);
                    gsap.from(chars, {
                        yPercent: 130,
                        opacity: 0,
                        rotate: 6,
                        duration: 0.7,
                        ease: 'back.out(1.6)',
                        stagger: 0.028,
                        scrollTrigger: { trigger: el, start: 'top 88%', once: true },
                    });
                });

                // ── Scroll-scrubbed rotation ──
                root.querySelectorAll('[data-fx="rotate"]').forEach((el) => {
                    const turns = parseFloat(el.dataset.turns ?? '0.5');
                    gsap.to(el, {
                        rotation: 360 * turns,
                        ease: 'none',
                        scrollTrigger: { trigger: el.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
                    });
                });

                // ── SVG path draw ──
                root.querySelectorAll('[data-fx="draw"]').forEach((svg) => {
                    svg.querySelectorAll('path').forEach((path) => {
                        const len = path.getTotalLength();
                        gsap.fromTo(path,
                            { strokeDasharray: len, strokeDashoffset: len },
                            {
                                strokeDashoffset: 0,
                                ease: 'none',
                                scrollTrigger: { trigger: svg, start: 'top 90%', end: 'top 35%', scrub: true },
                            });
                    });
                });

                // ── Pinned horizontal scroll ──
                root.querySelectorAll('[data-fx="hscroll"]').forEach((sec) => {
                    const track = sec.querySelector('[data-fx-track]');
                    if (!track) return;
                    const amount = () => Math.max(0, track.scrollWidth - sec.clientWidth);
                    gsap.to(track, {
                        x: () => -amount(),
                        ease: 'none',
                        scrollTrigger: {
                            trigger: sec,
                            start: 'top top',
                            end: () => '+=' + amount(),
                            scrub: true,
                            pin: true,
                            anticipatePin: 1,
                            invalidateOnRefresh: true,
                        },
                    });
                });

                // ── Batched child entrance ──
                root.querySelectorAll('[data-fx="batch"]').forEach((wrap) => {
                    gsap.from(wrap.children, {
                        y: 48,
                        opacity: 0,
                        duration: 0.8,
                        ease: 'power3.out',
                        stagger: 0.09,
                        scrollTrigger: { trigger: wrap, start: 'top 85%', once: true },
                    });
                });
            });
        }, root);

        // Recalculate after images settle (photo-heavy pages shift layout).
        setTimeout(() => ScrollTrigger.refresh(), 600);
    });

    onUnmounted(() => ctx?.revert());
}
