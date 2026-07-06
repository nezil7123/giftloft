let scriptPromise;

function loadScript() {
    scriptPromise ??= new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://checkout.razorpay.com/v1/checkout.js';
        s.onload = resolve;
        s.onerror = () => {
            scriptPromise = null;
            reject(new Error('Could not load the payment widget. Check your connection and try again.'));
        };
        document.head.appendChild(s);
    });

    return scriptPromise;
}

/**
 * Open the Razorpay checkout widget for a server-created order.
 *
 * openRazorpay({
 *   key, order: { id, amount, currency },
 *   description, prefill: { name, email, contact },
 *   onSuccess(response) — called with razorpay_order_id / payment_id / signature,
 *   onDismiss() — buyer closed the widget without paying.
 * })
 */
export async function openRazorpay({ key, order, description = '', prefill = {}, onSuccess, onDismiss }) {
    await loadScript();

    const rzp = new window.Razorpay({
        key,
        order_id: order.id,
        amount: order.amount,
        currency: order.currency ?? 'INR',
        name: 'Gift Loft',
        description,
        prefill,
        theme: { color: '#6366f1' },
        handler: (response) => onSuccess(response),
        modal: { ondismiss: () => onDismiss?.() },
    });

    rzp.open();
}
