import type { CalculatorValues } from "./service-calculator";

export type CctvStorageResult = {
    bitrateMbpsPerCamera: number;
    recordingHoursEquivalent: number;
    rawTb: number;
    requiredTb: number;
};

const CODEC_FACTOR: Record<string, number> = {
    h264: 0.095,
    h265: 0.06,
    h265plus: 0.045,
};

function bounded(value: unknown, min: number, max: number) {
    const numeric = Number(value);
    return Math.min(max, Math.max(min, Number.isFinite(numeric) ? numeric : min));
}

export function calculateCctvStorage(values: CalculatorValues): CctvStorageResult | null {
    const cameraCount = bounded(values.camera_count, 0, 256);
    if (cameraCount <= 0) return null;

    const megapixels = bounded(String(values.resolution ?? "4mp").replace(/[^\d.]/g, ""), 1, 64);
    const fps = bounded(values.recording_fps ?? 15, 1, 60);
    const codec = String(values.recording_codec ?? "h265").toLowerCase();
    const manualBitrate = bounded(values.video_bitrate_mbps ?? 0, 0, 128);
    const bitrateMbpsPerCamera = manualBitrate > 0
        ? manualBitrate
        : Math.max(0.4, megapixels * fps * (CODEC_FACTOR[codec] ?? CODEC_FACTOR.h265));
    const days = bounded(values.recording_days ?? 14, 1, 365);
    const mode = String(values.recording_mode ?? "continuous");
    const hours = mode === "continuous"
        ? 24
        : bounded(values.recording_hours_per_day ?? 12, 0, 24);
    const duty = mode === "motion"
        ? bounded(values.motion_recording_percent ?? 35, 0, 100) / 100
        : 1;
    const recordingHoursEquivalent = hours * duty;
    const reserve = bounded(values.storage_reserve_percent ?? 20, 0, 100) / 100;
    const rawTb = bitrateMbpsPerCamera * 1_000_000 / 8 * 3_600
        * recordingHoursEquivalent * days * cameraCount / 1_000_000_000_000;

    return {
        bitrateMbpsPerCamera: Math.round(bitrateMbpsPerCamera * 100) / 100,
        recordingHoursEquivalent: Math.round(recordingHoursEquivalent * 100) / 100,
        rawTb: Math.round(rawTb * 1_000) / 1_000,
        requiredTb: Math.round(rawTb * (1 + reserve) * 1_000) / 1_000,
    };
}
