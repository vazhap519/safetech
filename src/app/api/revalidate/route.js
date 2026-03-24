import { revalidateTag } from "next/cache";
import { NextResponse } from "next/server";

export async function POST(req) {
  const { tag } = await req.json();

  revalidateTag(tag);

  return NextResponse.json({ success: true });
}