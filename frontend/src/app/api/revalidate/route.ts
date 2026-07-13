import { revalidatePath, revalidateTag } from "next/cache";
import { NextResponse, type NextRequest } from "next/server";

type RevalidatePayload = {
    path?: string;
    tag?: string;
};

export async function POST(request: NextRequest) {
    try {
        const secret = request.headers.get("x-secret");

        if (!process.env.REVALIDATE_SECRET || secret !== process.env.REVALIDATE_SECRET) {
            return NextResponse.json(
                { success: false, message: "Unauthorized" },
                { status: 401 },
            );
        }

        const body = (await request.json()) as RevalidatePayload;
        const path = typeof body.path === "string" ? body.path.trim() : "";
        const tag = typeof body.tag === "string" ? body.tag.trim() : "";

        if (!tag && !path) {
            return NextResponse.json(
                { success: false, message: "Tag or path required" },
                { status: 400 },
            );
        }

        if (tag) {
            revalidateTag(tag, "max");
        }

        if (path) {
            revalidatePath(path);
        }

        return NextResponse.json({
            success: true,
            revalidated: {
                tag: tag || null,
                path: path || null,
            },
        });
    } catch {
        return NextResponse.json(
            { success: false, message: "Server error" },
            { status: 500 },
        );
    }
}
