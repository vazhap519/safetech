import { forwardBackendRequest } from "@/lib/backend-proxy";

export async function POST(request: Request) {
    return forwardBackendRequest(request, "/analytics/events");
}
