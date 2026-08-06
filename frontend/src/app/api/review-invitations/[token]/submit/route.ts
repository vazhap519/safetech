import { forwardBackendRequest } from "@/lib/backend-proxy";

export async function POST(
    request: Request,
    { params }: { params: Promise<{ token: string }> },
) {
    const { token } = await params;

    return forwardBackendRequest(
        request,
        `/review-invitations/${encodeURIComponent(token)}/submit`,
    );
}
