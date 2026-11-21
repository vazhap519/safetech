export type CameraRecordingProfile = {
    megapixels: number;
    fps: number;
    codec: "h264" | "h265" | "h265plus";
    bitrateMbps: number;
    audioMbps?: number;
    recordingMode: "continuous" | "schedule" | "motion";
    hoursPerDay: number;
    motionPercent: number;
};

export type RecorderProfile = {
    channels: number;
    incomingBandwidthMbps: number;
    sataBays: number;
    maxDiskTb: number;
    raid: "none" | "raid1" | "raid5";
};

const CODEC_FACTOR = { h264: 0.095, h265: 0.06, h265plus: 0.045 } as const;

export function cameraBitrate(profile: CameraRecordingProfile) {
    return profile.bitrateMbps > 0
        ? profile.bitrateMbps + Math.max(0, profile.audioMbps ?? 0)
        : Math.max(0.4, profile.megapixels * profile.fps * CODEC_FACTOR[profile.codec])
            + Math.max(0, profile.audioMbps ?? 0);
}

export function calculateCameraSystem(
    cameras: CameraRecordingProfile[],
    days: number,
    reservePercent: number,
    recorder: RecorderProfile,
) {
    const safeDays = Math.min(365, Math.max(1, days));
    const storageRawTb = cameras.reduce((total, camera) => {
        const hours = camera.recordingMode === "continuous"
            ? 24
            : Math.min(24, Math.max(0, camera.hoursPerDay));
        const duty = camera.recordingMode === "motion"
            ? Math.min(100, Math.max(0, camera.motionPercent)) / 100
            : 1;
        return total + cameraBitrate(camera) * 1_000_000 / 8 * 3_600 * hours
            * duty * safeDays / 1_000_000_000_000;
    }, 0);
    const storageRequiredTb = storageRawTb * (1 + Math.min(100, Math.max(0, reservePercent)) / 100);
    const bandwidthMbps = cameras.reduce((total, camera) => total + cameraBitrate(camera), 0);
    const raidUsableFactor = recorder.raid === "raid1"
        ? (recorder.sataBays >= 2 ? Math.floor(recorder.sataBays / 2) / recorder.sataBays : 0)
        : recorder.raid === "raid5"
            ? (recorder.sataBays >= 3 ? (recorder.sataBays - 1) / recorder.sataBays : 0)
            : 1;
    const usableStorageTb = recorder.sataBays * recorder.maxDiskTb * raidUsableFactor;
    const issues: string[] = [];
    if (cameras.length > recorder.channels) issues.push("channels");
    if (bandwidthMbps > recorder.incomingBandwidthMbps) issues.push("bandwidth");
    if (storageRequiredTb > usableStorageTb) issues.push("storage");
    if (raidUsableFactor === 0) issues.push("raid");

    const dataDiskCount = Math.ceil(storageRequiredTb / Math.max(1, recorder.maxDiskTb));
    const requiredDiskCount = recorder.raid === "raid1"
        ? dataDiskCount * 2
        : recorder.raid === "raid5"
            ? Math.max(3, dataDiskCount + 1)
            : dataDiskCount;

    return {
        storageRawTb: Math.round(storageRawTb * 1_000) / 1_000,
        storageRequiredTb: Math.round(storageRequiredTb * 1_000) / 1_000,
        bandwidthMbps: Math.round(bandwidthMbps * 100) / 100,
        usableStorageTb: Math.round(usableStorageTb * 100) / 100,
        requiredDiskCount,
        valid: issues.length === 0,
        issues,
    };
}
