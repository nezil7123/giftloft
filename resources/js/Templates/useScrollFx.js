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
 *   data-fx="words"                        — splits text into words, softer staggered rise
 *   data-fx="img-reveal"                   — clip-path wipe reveal (put on the image's wrapper)
 *   data-fx="rotate"    data-turns="0.5"   — rotation scrubbed across the viewport pass
 *   data-fx="draw"                         — SVG path stroke draws itself on scrub (put on <svg>)
 *   data-fx="hscroll"                      — pinned section; child [data-fx-track] scrolls horizontally
 *   data-fx="batch"                        — children fade-rise in with stagger when entering
 *   data-fx="tilt3d"                       — card tilts in 3D following the pointer
 *   data-fx="decrypt"                      — text scrambles through glyphs, then settles (on enter)
 *   data-fx="rise3d"                       — children flip up from 3D depth with stagger (on enter)
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
                // Manual listeners registered here; matchMedia calls the
                // returned cleanup when the context reverts.
                const cleanups = [];

                // ── Pointer-tracked 3D tilt ──
                root.querySelectorAll('[data-fx="tilt3d"]').forEach((el) => {
                    gsap.set(el, { transformPerspective: 800 });
                    const rx = gsap.quickTo(el, 'rotationX', { duration: 0.45, ease: 'power2.out' });
                    const ry = gsap.quickTo(el, 'rotationY', { duration: 0.45, ease: 'power2.out' });
                    const move = (e) => {
                        const r = el.getBoundingClientRect();
                        rx(((e.clientY - r.top) / r.height - 0.5) * -8);
                        ry(((e.clientX - r.left) / r.width - 0.5) * 10);
                    };
                    const leave = () => { rx(0); ry(0); };
                    el.addEventListener('pointermove', move);
                    el.addEventListener('pointerleave', leave);
                    cleanups.push(() => {
                        el.removeEventListener('pointermove', move);
                        el.removeEventListener('pointerleave', leave);
                    });
                });

                // ── Decrypt / scramble text ──
                root.querySelectorAll('[data-fx="decrypt"]').forEach((el) => {
                    if (el.dataset.fxSplit) return;
                    el.dataset.fxSplit = '1';
                    const finalText = el.textContent;
                    const glyphs = '█▓▒░<>/=+*#XKZQ0147';
                    const state = { p: 0 };
                    gsap.to(state, {
                        p: 1,
                        duration: 1.1,
                        ease: 'power1.inOut',
                        scrollTrigger: { trigger: el, start: 'top 90%', once: true },
                        onUpdate() {
                            const reveal = Math.floor(state.p * finalText.length);
                            el.textContent = finalText.slice(0, reveal)
                                + [...finalText.slice(reveal)].map((c) => (c === ' ' ? ' ' : glyphs[(Math.random() * glyphs.length) | 0])).join('');
                        },
                        onComplete() { el.textContent = finalText; },
                    });
                });

                // ── Children flip up from 3D depth ──
                root.querySelectorAll('[data-fx="rise3d"]').forEach((wrap) => {
                    gsap.set(wrap, { perspective: 900 });
                    gsap.from(wrap.children, {
                        rotationX: 28,
                        y: 90,
                        z: -120,
                        opacity: 0,
                        transformOrigin: '50% 100%',
                        duration: 0.9,
                        ease: 'power3.out',
                        stagger: 0.08,
                        scrollTrigger: { trigger: wrap, start: 'top 85%', once: true },
                        // Re-center the pivot so a tilt3d on the same element
                        // rotates around its middle, not its bottom edge.
                        onComplete() { gsap.set(wrap.children, { transformOrigin: '50% 50%' }); },
                    });
                });

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
                    // Descend into a sole wrapping element (e.g. a gradient
                    // bg-clip-text <span>) so its styling survives the split.
                    while (el.childElementCount === 1 && el.firstElementChild.textContent.trim() === el.textContent.trim()) {
                        el = el.firstElementChild;
                    }
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

                // ── Word split reveal ──
                root.querySelectorAll('[data-fx="words"]').forEach((el) => {
                    if (el.dataset.fxSplit) return;
                    el.dataset.fxSplit = '1';
                    // Spaces must live OUTSIDE the inline-block spans (as plain
                    // text nodes) or the browser collapses them and the words
                    // run together. Line breaks are kept as <br>.
                    const frag = document.createDocumentFragment();
                    const words = [];
                    el.textContent.trim().split(/\n+/).forEach((line, li) => {
                        if (li) frag.append(document.createElement('br'));
                        line.trim().split(/\s+/).forEach((w) => {
                            const s = document.createElement('span');
                            s.textContent = w;
                            s.style.display = 'inline-block';
                            words.push(s);
                            frag.append(s, document.createTextNode(' '));
                        });
                    });
                    el.textContent = '';
                    el.append(frag);
                    gsap.from(words, {
                        y: 34,
                        opacity: 0,
                        duration: 0.85,
                        ease: 'power3.out',
                        stagger: 0.035,
                        scrollTrigger: { trigger: el, start: 'top 86%', once: true },
                    });
                });

                // ── Clip-path image wipe ──
                root.querySelectorAll('[data-fx="img-reveal"]').forEach((el) => {
                    const img = el.querySelector('img');
                    // Match the element's own corner radius so the final clip
                    // lines up exactly with its rounded-* class.
                    const radius = getComputedStyle(el).borderRadius || '0px';
                    gsap.fromTo(el,
                        { clipPath: `inset(12% 12% 12% 12% round ${radius})` },
                        {
                            clipPath: `inset(0% 0% 0% 0% round ${radius})`,
                            ease: 'none',
                            scrollTrigger: { trigger: el, start: 'top 92%', end: 'top 45%', scrub: true },
                        });
                    if (img) {
                        gsap.fromTo(img, { scale: 1.25 }, {
                            scale: 1,
                            ease: 'none',
                            scrollTrigger: { trigger: el, start: 'top 92%', end: 'top 35%', scrub: true },
                        });
                    }
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

                return () => cleanups.forEach((fn) => fn());
            });
        }, root);

        // Recalculate after images settle (photo-heavy pages shift layout).
        setTimeout(() => ScrollTrigger.refresh(), 600);
    });

    onUnmounted(() => ctx?.revert());
}
