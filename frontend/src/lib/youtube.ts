const YOUTUBE_ID_PATTERN = /^[a-zA-Z0-9_-]{11}$/;

function getYouTubeVideoId(url?: string | null) {
    const value = url?.trim();

    if (!value) return null;
    if (YOUTUBE_ID_PATTERN.test(value)) return value;

    try {
        const parsed = new URL(
            value.startsWith("http") ? value : `https://${value}`,
        );
        const hostname = parsed.hostname.replace(/^www\./, "");

        if (hostname === "youtu.be") {
            const id = parsed.pathname.split("/").filter(Boolean)[0];
            return id && YOUTUBE_ID_PATTERN.test(id) ? id : null;
        }

        if (hostname !== "youtube.com" && !hostname.endsWith(".youtube.com")) {
            return null;
        }

        const watchId = parsed.searchParams.get("v");
        if (watchId && YOUTUBE_ID_PATTERN.test(watchId)) return watchId;

        const [, route, id] = parsed.pathname.split("/");

        if (
            ["embed", "shorts", "live"].includes(route) &&
            YOUTUBE_ID_PATTERN.test(id)
        ) {
            return id;
        }
    } catch {
        return null;
    }

    return null;
}

export function getYouTubeWatchUrl(url?: string | null) {
    const videoId = getYouTubeVideoId(url);

    return videoId ? `https://www.youtube.com/watch?v=${videoId}` : "";
}

export function getYouTubeEmbedUrl(url?: string | null) {
    const videoId = getYouTubeVideoId(url);

    return videoId
        ? `https://www.youtube-nocookie.com/embed/${videoId}?rel=0&modestbranding=1`
        : "";
}
