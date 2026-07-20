import { Fragment } from "react";

import type { StructuredDataValue } from "@/lib/structured-data";

export default function JsonLd({ data }: { data?: StructuredDataValue | null }) {
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
