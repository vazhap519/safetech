import { revalidateTag, revalidatePath } from "next/cache";
import { NextResponse } from "next/server";

export async function POST(req) {
    try {
    const body = await req.json();

    const secret = req.headers.get("x-secret");

    // 🔐 SECURITY
    if (secret !== process.env.REVALIDATE_SECRET) {
      return NextResponse.json(
        { success: false, message: "Unauthorized" },
        { status: 401 }
      );
    }

    const { tag, path } = body;

    // 🔍 validation
    if (!tag && !path) {
      return NextResponse.json(
        { success: false, message: "Tag or path required" },
        { status: 400 }
      );
    }

    // 🔥 TAG REVALIDATION
    if (tag) {
      revalidateTag(tag);
    }

    // 🔥 PATH REVALIDATION
    if (path) {
      revalidatePath(path);
    }

    return NextResponse.json({
      success: true,
      revalidated: {
        tag: tag ?? null,
        path: path ?? null,
      },
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, message: "Server error" },
      { status: 500 }
    );
  }
}