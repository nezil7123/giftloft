import { onMounted, onUnmounted } from 'vue';
import { gsap } from 'gsap';

/**
 * GSAP polish for invitation cards:
 *
 *  - Entrance: elements marked [data-card] play a staggered rise/blur-in
 *    timeline on mount (order = DOM order).
 *  - Tilt: the card follows the pointer in 3D (perspective tilt + lift),
 *    springing back on leave. An optional [data-card-shine] element is
 *    moved with the pointer to fake a light sweep across the card.
 *
 * Call with a ref on the card's outermost element:
 *   const card = ref(null);
 *   useCardFx(card);
 *
 * Reduced-motion users get the finished layout with no tilt.
 */
export function useCardFx(cardRef) {
    let ctx;
    let cleanupPointer;

    onMounted(() => {
        const card = cardRef?.value;
        if (!card) return;

        ctx = gsap.context(() => {
            const mm = gsap.matchMedia();

            mm.add('(prefers-reduced-motion: no-preference)', () => {
                // ── Entrance timeline ──
                const parts = card.querySelectorAll('[data-card]');
                gsap.timeline({ defaults: { ease: 'power3.out' } })
                    .from(card, { y: 44, opacity: 0, rotateX: 14, transformPerspective: 900, duration: 0.9, ease: 'power4.out' })
                    .from(parts, { y: 26, opacity: 0, filter: 'blur(6px)', duration: 0.7, stagger: 0.08 }, '-=0.45');

                // ── Pointer tilt ──
                gsap.set(card, { transformPerspective: 900 });
                const rx = gsap.quickTo(card, 'rotationX', { duration: 0.5, ease: 'power2.out' });
                const ry = gsap.quickTo(card, 'rotationY', { duration: 0.5, ease: 'power2.out' });
                const shine = card.querySelector('[data-card-shine]');
                const sx = shine ? gsap.quickTo(shine, 'xPercent', { duration: 0.5, ease: 'power2.out' }) : null;
                const sy = shine ? gsap.quickTo(shine, 'yPercent', { duration: 0.5, ease: 'power2.out' }) : null;

                const move = (e) => {
                    const r = card.getBoundingClientRect();
                    const px = (e.clientX - r.left) / r.width - 0.5;   // -0.5 … 0.5
                    const py = (e.clientY - r.top) / r.height - 0.5;
                    rx(py * -10);
                    ry(px * 12);
                    sx?.(px * 90);
                    sy?.(py * 90);
                };
                const leave = () => {
                    rx(0);
                    ry(0);
                    sx?.(0);
                    sy?.(0);
                };

                card.addEventListener('pointermove', move);
                card.addEventListener('pointerleave', leave);
                cleanupPointer = () => {
                    card.removeEventListener('pointermove', move);
                    card.removeEventListener('pointerleave', leave);
                };
            });
        }, card);
    });

    onUnmounted(() => {
        cleanupPointer?.();
        ctx?.revert();
    });
}
