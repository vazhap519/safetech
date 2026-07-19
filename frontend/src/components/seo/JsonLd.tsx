import { Fragment } from "react";

export type JsonLdValue = Record<string, unknown> | Array<Record<string, unknown>>;

export default function JsonLd({ data }: { data?: JsonLdValue | null }) {
    if (!data) return null;

    const items = Array.isArray(data) ? data : [data];

    return items.filter(Boolean).map((item, index) => (
        <Fragment key={index}>
            <script
                dangerouslySetInnerHTML={{
                    __html: JSON.stringify(item).replace(/</g, "\\u003c"),
                }}
                type="application/ld+json"
            />
        </Fragment>
    ));
}
