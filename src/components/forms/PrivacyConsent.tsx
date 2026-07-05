export default function PrivacyConsent() {
    return (
        <label className="flex items-start gap-3 text-sm leading-relaxed text-on-surface-variant">
            <input className="mt-1 size-4 shrink-0 accent-primary-container" name="privacy" required type="checkbox" value="accepted" />
            <span>ვეთანხმები ჩემი საკონტაქტო მონაცემების დამუშავებას მოთხოვნაზე პასუხის გასაცემად.</span>
        </label>
    );
}
