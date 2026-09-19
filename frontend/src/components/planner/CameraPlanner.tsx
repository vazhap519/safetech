"use client";

import { useCallback, useEffect, useMemo, useRef, useState, type FormEvent, type PointerEvent } from "react";
import { useLocalization } from "@/components/providers/LocalizationProvider";

type Point = { x: number; y: number };
type Wall = { ax: number; ay: number; bx: number; by: number };
type Kind = "bullet" | "dome" | "ptz" | "turret";
type Camera = Point & {
    id: string; direction: number; fov: number; range: number;
    kind: Kind; height: number; lens: number; sensor: number;
};
type Layout = {
    version: 1; widthMeters: number; cameras: Camera[];
    walls: Wall[]; area: Point[];
};
type Mode = "select" | "camera" | "wall" | "area";
const W = 900, H = 600;
const initial: Layout = { version: 1, widthMeters: 20, cameras: [], walls: [], area: [] };
const kinds: Kind[] = ["bullet", "dome", "ptz", "turret"];
const copy = {
    ka: {
        title: "კამერების განლაგების პლანერი",
        subtitle: "ატვირთეთ ობიექტის გეგმა ან ფოტო, მონიშნეთ კედლები, კამერები და შესამოწმებელი არე. შეაფასეთ ხედვის ზონები და გამოგვიგზავნეთ პროექტი.",
        upload: "გეგმის / ფოტოს ატვირთვა", import: "JSON გეგმის გახსნა", wall: "კედლის დახაზვა",
        area: "საკონტროლო არე", camera: "კამერის დამატება", select: "გადატანა / არჩევა",
        close: "არის დასრულება", undo: "უკან", clear: "თავიდან", save: "JSON ჩამოტვირთვა",
        image: "სქემის SVG ჩამოტვირთვა", width: "გეგმის სრული სიგანე (მ)", preview: "კამერის პარამეტრები",
        delete: "კამერის წაშლა", count: "კამერები", walls: "კედლები", covered: "არეის დაფარვა",
        blind: "სავარაუდო ბრმა ზონები", noArea: "დაფარვის %-ისთვის დახაზეთ საკონტროლო არე",
        name: "სახელი / კომპანია", phone: "ტელეფონი", email: "ელფოსტა (არასავალდებულო)",
        project: "ობიექტის დასახელება", privacy: "თანახმა ვარ, რომ SafeTech დამიკავშირდეს პროექტის შესახებ.",
        submit: "გეგმის გაგზავნა SafeTech-ში", sending: "იგზავნება...", sent: "გეგმა წარმატებით გაიგზავნა!",
        error: "ვერ გაიგზავნა. შეამოწმეთ ინტერნეტი და სცადეთ თავიდან.",
        disclaimer: "მნიშვნელოვანი: ეს არის 2D მიახლოებითი დაგეგმვა და არა კომპიუტერული ხედვით ავტომატური ამოცნობა. მხოლოდ ფოტოდან მასშტაბი, კედლის სიმაღლე, ოპტიკა ან რეალური ბრმა ზონები არ განისაზღვრება. მიუთითეთ რეალური სიგანე და ადგილზე გადაამოწმეთ შედეგი.",
        tip: "აირჩიეთ რეჟიმი და დააჭირეთ ნახაზზე. კედელს სჭირდება ორი წერტილი. არის დასასრულებლად დააჭირეთ „არის დასრულება“. კამერა გადაათრიეთ თითით ან მაუსით.",
        fov: "ხედვის კუთხე (°)", range: "ხილვადობის მანძილი (მ)", angle: "მიმართულება (°)",
        height: "მონტაჟის სიმაღლე (მ)", lens: "ობიექტივი (მმ)", sensor: "სენსორის სიგანე (მმ)",
        clearConfirm: "გსურთ ამ გეგმის მთლიანად წაშლა?", cameraType: "კამერის ტიპი",
        view: "მიახლოებითი ხედვა", photograph: "ფოტო ინახება მხოლოდ მოთხოვნის გაგზავნისას ან JSON ფაილში.",
    },
    en: {
        title: "CCTV camera layout planner",
        subtitle: "Upload a plan or photo, mark walls, cameras and the area to inspect. Estimate coverage and send your design.",
        upload: "Upload plan / photo", import: "Open JSON design", wall: "Draw wall", area: "Inspection area",
        camera: "Add camera", select: "Select / drag", close: "Finish area", undo: "Undo", clear: "Reset",
        save: "Download JSON", image: "Download SVG", width: "Full plan width (m)", preview: "Camera settings",
        delete: "Delete camera", count: "Cameras", walls: "Walls", covered: "Area coverage",
        blind: "Estimated blind spots", noArea: "Outline the inspection area for a coverage %",
        name: "Name / company", phone: "Phone", email: "Email (optional)", project: "Project name",
        privacy: "I agree that SafeTech may contact me about this project.",
        submit: "Send design to SafeTech", sending: "Sending...", sent: "Design submitted successfully!",
        error: "Submission failed. Check your connection and retry.",
        disclaimer: "Important: this is approximate 2D planning, not automated photo interpretation. A photograph alone cannot determine scale, wall height, optics or true blind spots. Enter measured width and verify on site.",
        tip: "Select a tool and tap the plan. A wall uses two points. Finish your inspection polygon with “Finish area”. Drag cameras with mouse or touch.",
        fov: "Field of view (°)", range: "View range (m)", angle: "Direction (°)", height: "Mount height (m)",
        lens: "Lens (mm)", sensor: "Sensor width (mm)", clearConfirm: "Delete the entire plan?",
        cameraType: "Camera type", view: "Approximate field of view", photograph: "Photo is only stored when submitting or exporting JSON.",
    },
    ru: {
        title: "Планировщик размещения камер",
        subtitle: "Загрузите план или фото, отметьте стены, камеры и зону контроля. Оцените покрытие и отправьте проект.",
        upload: "Загрузить план / фото", import: "Открыть JSON", wall: "Нарисовать стену",
        area: "Зона контроля", camera: "Добавить камеру", select: "Выбрать / переместить",
        close: "Завершить зону", undo: "Отменить", clear: "Очистить", save: "Скачать JSON",
        image: "Скачать SVG", width: "Полная ширина плана (м)", preview: "Настройки камеры",
        delete: "Удалить камеру", count: "Камеры", walls: "Стены", covered: "Покрытие зоны",
        blind: "Оценка слепых зон", noArea: "Обведите зону контроля для процента покрытия",
        name: "Имя / компания", phone: "Телефон", email: "Почта (необязательно)",
        project: "Название объекта", privacy: "Согласен на связь с SafeTech по проекту.",
        submit: "Отправить проект SafeTech", sending: "Отправка...", sent: "Проект отправлен!",
        error: "Ошибка отправки. Проверьте соединение.",
        disclaimer: "Важно: это приблизительное 2D-планирование, а не автоматический анализ фотографии. По фото нельзя определить масштаб, высоту стен, оптику или реальные слепые зоны. Укажите измеренную ширину и проверьте на объекте.",
        tip: "Выберите инструмент и нажмите на план. Стена строится по двум точкам. Завершите контур кнопкой «Завершить зону». Перетаскивайте камеры пальцем или мышью.",
        fov: "Угол обзора (°)", range: "Дальность (м)", angle: "Направление (°)",
        height: "Высота монтажа (м)", lens: "Объектив (мм)", sensor: "Ширина сенсора (мм)",
        clearConfirm: "Удалить весь проект?", cameraType: "Тип камеры",
        view: "Приблизительный угол обзора", photograph: "Фото сохраняется только при отправке или экспорте JSON.",
    },
} as const;

function clamp(n: number, low: number, high: number) {
    return Math.max(low, Math.min(high, Number.isFinite(n) ? n : low));
}
function cross(a: Point, b: Point) { return a.x * b.y - a.y * b.x; }
function subtract(a: Point, b: Point): Point { return { x: a.x - b.x, y: a.y - b.y }; }
function rayHit(a: Point, ray: Point, wall: Wall): number | null {
    const b = { x: wall.ax, y: wall.ay }, s = { x: wall.bx - wall.ax, y: wall.by - wall.ay };
    const den = cross(ray, s);
    if (Math.abs(den) < 1e-8) return null;
    const diff = subtract(b, a), t = cross(diff, s) / den, u = cross(diff, ray) / den;
    return t > 0.001 && u >= 0 && u <= 1 ? t : null;
}
const borders: Wall[] = [
    { ax: 0, ay: 0, bx: W, by: 0 }, { ax: W, ay: 0, bx: W, by: H },
    { ax: W, ay: H, bx: 0, by: H }, { ax: 0, ay: H, bx: 0, by: 0 },
];
function endpoint(camera: Camera, angle: number, layout: Layout): Point {
    const ray = { x: Math.cos(angle), y: Math.sin(angle) };
    let dist = camera.range * W / layout.widthMeters;
    for (const wall of [...layout.walls, ...borders]) {
        const t = rayHit(camera, ray, wall);
        if (t !== null) dist = Math.min(dist, t);
    }
    return { x: clamp(camera.x + ray.x * dist, 0, W), y: clamp(camera.y + ray.y * dist, 0, H) };
}
function inPolygon(p: Point, polygon: Point[]): boolean {
    let inside = false;
    for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
        const a = polygon[i], b = polygon[j];
        if ((a.y > p.y) !== (b.y > p.y) &&
            p.x < (b.x - a.x) * (p.y - a.y) / (b.y - a.y) + a.x) inside = !inside;
    }
    return inside;
}
function visible(p: Point, c: Camera, layout: Layout): boolean {
    const delta = subtract(p, c), length = Math.hypot(delta.x, delta.y);
    if (length > c.range * W / layout.widthMeters || length < 0.01) return length < 0.01;
    const direction = c.direction * Math.PI / 180;
    const difference = Math.atan2(Math.sin(Math.atan2(delta.y, delta.x) - direction),
        Math.cos(Math.atan2(delta.y, delta.x) - direction));
    if (Math.abs(difference) > c.fov * Math.PI / 360) return false;
    const ray = { x: delta.x / length, y: delta.y / length };
    return layout.walls.every((wall) => {
        const hit = rayHit(c, ray, wall);
        return hit === null || hit >= length - 0.01;
    });
}
type Analysis = { percent: number | null; blind: Point[]; count: number };
function analyse(layout: Layout): Analysis {
    if (layout.area.length < 3) return { percent: null, blind: [], count: 0 };
    const blind: Point[] = [];
    let total = 0, covered = 0;
    for (let y = 12; y < H; y += 24) for (let x = 12; x < W; x += 24) {
        const p = { x, y };
        if (!inPolygon(p, layout.area)) continue;
        total++;
        if (layout.cameras.some((c) => visible(p, c, layout))) covered++;
        else blind.push(p);
    }
    return { percent: total ? Math.round(covered * 100 / total) : null, blind, count: total };
}
function readFile(file: File): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(String(reader.result));
        reader.onerror = () => reject(new Error("File read failed"));
        reader.readAsDataURL(file);
    });
}
function download(name: string, blob: Blob) {
    const url = URL.createObjectURL(blob), a = document.createElement("a");
    a.href = url; a.download = name; document.body.append(a); a.click(); a.remove();
    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}
function cleanLayout(data: unknown): Layout {
    if (!data || typeof data !== "object") throw new Error("Invalid design");
    const p = data as Partial<Layout>;
    if (!Array.isArray(p.cameras) || !Array.isArray(p.walls) || !Array.isArray(p.area))
        throw new Error("Invalid design");
    const point = (value: unknown): Point => {
        const q = value as Point;
        return { x: clamp(Number(q?.x), 0, W), y: clamp(Number(q?.y), 0, H) };
    };
    return {
        version: 1, widthMeters: clamp(Number(p.widthMeters), 1, 500),
        cameras: p.cameras.slice(0, 128).map((camera, i) => ({
            ...point(camera), id: String(camera.id || i), direction: clamp(Number(camera.direction), 0, 360),
            fov: clamp(Number(camera.fov), 15, 180), range: clamp(Number(camera.range), 1, 100),
            kind: kinds.includes(camera.kind) ? camera.kind : "bullet",
            height: clamp(Number(camera.height), 1, 20), lens: clamp(Number(camera.lens), 1, 20),
            sensor: clamp(Number(camera.sensor), 2, 20),
        })),
        walls: p.walls.slice(0, 256).map((w) => ({
            ax: point({ x: w.ax, y: w.ay }).x, ay: point({ x: w.ax, y: w.ay }).y,
            bx: point({ x: w.bx, y: w.by }).x, by: point({ x: w.bx, y: w.by }).y,
        })),
        area: p.area.slice(0, 80).map(point),
    };
}

export default function CameraPlanner() {
    const { locale } = useLocalization();
    const t = copy[locale as keyof typeof copy] || copy.ka;
    const canvas = useRef<HTMLCanvasElement>(null);
    const [layout, setLayout] = useState<Layout>(initial);
    const [background, setBackground] = useState<string>("");
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);
    const [mode, setMode] = useState<Mode>("camera");
    const [selected, setSelected] = useState<string | null>(null);
    const [wallStart, setWallStart] = useState<Point | null>(null);
    const [areaDraft, setAreaDraft] = useState<Point[]>([]);
    const [history, setHistory] = useState<Layout[]>([]);
    const [dragging, setDragging] = useState<string | null>(null);
    const [hydrated, setHydrated] = useState(false);
    const [status, setStatus] = useState("");
    const [sending, setSending] = useState(false);
    const [title, setTitle] = useState("");
    const [name, setName] = useState("");
    const [phone, setPhone] = useState("");
    const [email, setEmail] = useState("");
    const [privacy, setPrivacy] = useState(false);
    const analysis = useMemo(() => analyse(layout), [layout]);
    const active = layout.cameras.find((c) => c.id === selected) || null;
    const button = "rounded-xl border border-slate-600 px-4 py-3 text-sm font-semibold transition hover:border-amber-400";
    const field = "w-full rounded-xl border border-slate-600 bg-slate-900 p-3 text-slate-100 outline-none focus:border-amber-400";
    const pushHistory = useCallback(() => setHistory((previous) => [...previous.slice(-29), layout]), [layout]);

    useEffect(() => {
        try {
            const saved = localStorage.getItem("safetech-camera-planner-v1");
            if (saved) {
                const parsed = JSON.parse(saved);
                setLayout(cleanLayout(parsed.layout));
                if (typeof parsed.background === "string") setBackground(parsed.background);
            }
        } catch { /* Corrupt/quota-limited browser storage must never block the planner. */ }
        setHydrated(true);
    }, []);
    useEffect(() => {
        if (!hydrated) return;
        try {
            localStorage.setItem("safetech-camera-planner-v1", JSON.stringify({
                layout, background: background.length < 1200000 ? background : "",
            }));
        } catch { /* Large photos belong in explicit JSON exports. */ }
    }, [layout, background, hydrated]);
    useEffect(() => {
        if (!background) { setBgImage(null); return; }
        const image = new window.Image();
        image.onload = () => setBgImage(image);
        image.onerror = () => setBgImage(null);
        image.src = background;
    }, [background]);

    const render = useCallback((ctx: CanvasRenderingContext2D, withBlind: boolean) => {
        ctx.clearRect(0, 0, W, H);
        ctx.fillStyle = "#f8fafc"; ctx.fillRect(0, 0, W, H);
        if (bgImage) {
            const ratio = Math.min(W / bgImage.width, H / bgImage.height);
            const iw = bgImage.width * ratio, ih = bgImage.height * ratio;
            ctx.drawImage(bgImage, (W - iw) / 2, (H - ih) / 2, iw, ih);
            ctx.fillStyle = "#ffffff22"; ctx.fillRect(0, 0, W, H);
        }
        ctx.strokeStyle = "#64748b24"; ctx.lineWidth = 1;
        for (let x = 0; x <= W; x += 25) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
        }
        for (let y = 0; y <= H; y += 25) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
        }
        const polygon = (points: Point[]) => {
            if (!points.length) return;
            ctx.beginPath(); ctx.moveTo(points[0].x, points[0].y);
            for (const point of points.slice(1)) ctx.lineTo(point.x, point.y);
            ctx.closePath();
        };
        if (layout.area.length >= 3) {
            polygon(layout.area); ctx.fillStyle = "#22c55e13"; ctx.fill();
            ctx.strokeStyle = "#16a34a"; ctx.lineWidth = 3; ctx.stroke();
            if (withBlind) {
                ctx.fillStyle = "#ef444459";
                for (const point of analysis.blind) ctx.fillRect(point.x - 12, point.y - 12, 24, 24);
            }
        }
        for (const c of layout.cameras) {
            const steps = 36, begin = (c.direction - c.fov / 2) * Math.PI / 180;
            const points: Point[] = [c];
            for (let i = 0; i <= steps; i++)
                points.push(endpoint(c, begin + c.fov * Math.PI / 180 * i / steps, layout));
            polygon(points); ctx.fillStyle = "#f59e0b41"; ctx.fill();
            ctx.strokeStyle = "#d97706"; ctx.lineWidth = c.id === selected ? 3 : 1.5; ctx.stroke();
        }
        ctx.lineCap = "round";
        for (const wall of layout.walls) {
            ctx.beginPath(); ctx.moveTo(wall.ax, wall.ay); ctx.lineTo(wall.bx, wall.by);
            ctx.strokeStyle = "#1e293b"; ctx.lineWidth = 6; ctx.stroke();
        }
        if (wallStart) {
            ctx.beginPath(); ctx.arc(wallStart.x, wallStart.y, 6, 0, Math.PI * 2);
            ctx.fillStyle = "#0f172a"; ctx.fill();
        }
        if (areaDraft.length) {
            ctx.beginPath(); ctx.moveTo(areaDraft[0].x, areaDraft[0].y);
            for (const p of areaDraft.slice(1)) ctx.lineTo(p.x, p.y);
            ctx.strokeStyle = "#16a34a"; ctx.lineWidth = 3; ctx.setLineDash([7, 5]); ctx.stroke();
            ctx.setLineDash([]);
            for (const p of areaDraft) {
                ctx.beginPath(); ctx.arc(p.x, p.y, 5, 0, Math.PI * 2); ctx.fillStyle = "#16a34a"; ctx.fill();
            }
        }
        layout.cameras.forEach((c, i) => {
            ctx.beginPath(); ctx.arc(c.x, c.y, c.id === selected ? 15 : 12, 0, Math.PI * 2);
            ctx.fillStyle = c.id === selected ? "#1d4ed8" : "#0f172a"; ctx.fill();
            ctx.lineWidth = 2; ctx.strokeStyle = "#fff"; ctx.stroke();
            const a = c.direction * Math.PI / 180;
            ctx.beginPath(); ctx.moveTo(c.x, c.y);
            ctx.lineTo(c.x + Math.cos(a) * 22, c.y + Math.sin(a) * 22);
            ctx.strokeStyle = "#f59e0b"; ctx.lineWidth = 4; ctx.stroke();
            ctx.font = "bold 13px sans-serif"; ctx.fillStyle = "#fff";
            ctx.textAlign = "center"; ctx.textBaseline = "middle";
            ctx.fillText(String(i + 1), c.x, c.y);
        });
    }, [bgImage, layout, analysis, selected, wallStart, areaDraft]);
    useEffect(() => {
        const ctx = canvas.current?.getContext("2d");
        if (ctx) render(ctx, true);
    }, [render]);

    function coords(event: PointerEvent<HTMLCanvasElement>): Point {
        const box = event.currentTarget.getBoundingClientRect();
        return {
            x: clamp((event.clientX - box.left) * W / box.width, 0, W),
            y: clamp((event.clientY - box.top) * H / box.height, 0, H),
        };
    }
    function pointerDown(event: PointerEvent<HTMLCanvasElement>) {
        const p = coords(event);
        const near = [...layout.cameras].reverse().find((c) => Math.hypot(c.x - p.x, c.y - p.y) < 20);
        if (near && (mode === "select" || mode === "camera")) {
            pushHistory(); setSelected(near.id); setDragging(near.id);
            event.currentTarget.setPointerCapture(event.pointerId);
            return;
        }
        if (mode === "select") { setSelected(null); return; }
        if (mode === "camera" && layout.cameras.length < 128) {
            pushHistory();
            const newCamera: Camera = { ...p, id: crypto.randomUUID(), direction: 0, fov: 90,
                range: 18, kind: "bullet", height: 3, lens: 2.8, sensor: 5.6 };
            setLayout((value) => ({ ...value, cameras: [...value.cameras, newCamera] }));
            setSelected(newCamera.id);
        }
        if (mode === "wall") {
            if (!wallStart) setWallStart(p);
            else if (layout.walls.length < 256) {
                pushHistory();
                setLayout((value) => ({ ...value,
                    walls: [...value.walls, { ax: wallStart.x, ay: wallStart.y, bx: p.x, by: p.y }] }));
                setWallStart(null);
            }
        }
        if (mode === "area" && areaDraft.length < 80) setAreaDraft((value) => [...value, p]);
    }
    function pointerMove(event: PointerEvent<HTMLCanvasElement>) {
        if (!dragging) return;
        const p = coords(event);
        setLayout((value) => ({ ...value, cameras: value.cameras.map((c) =>
            c.id === dragging ? { ...c, ...p } : c) }));
    }
    function editCamera(key: keyof Camera, value: number | string) {
        if (!active) return;
        pushHistory();
        setLayout((old) => ({ ...old, cameras: old.cameras.map((c) =>
            c.id === active.id ? { ...c, [key]: value } : c) }));
    }
    async function handlePhoto(file?: File) {
        if (!file) return;
        if (!["image/png", "image/jpeg", "image/webp"].includes(file.type) || file.size > 5 * 1024 * 1024) {
            setStatus("PNG / JPEG / WebP · max 5 MB"); return;
        }
        try { setBackground(await readFile(file)); setStatus(""); }
        catch { setStatus(t.error); }
    }
    async function handleJSON(file?: File) {
        if (!file || file.size > 8 * 1024 * 1024) return;
        try {
            const parsed = JSON.parse(await file.text());
            pushHistory(); setLayout(cleanLayout(parsed.layout || parsed));
            setBackground(typeof parsed.background === "string" && parsed.background.startsWith("data:image/")
                ? parsed.background : "");
            setWallStart(null); setAreaDraft([]); setSelected(null); setStatus("");
        } catch { setStatus("Invalid JSON design"); }
    }
    function exportJSON() {
        download("safetech-camera-plan.json", new Blob(
            [JSON.stringify({ layout, background }, null, 2)], { type: "application/json" }));
    }
    function exportSVG() {
        // SVG preview is intentionally self-contained; the original photo is in the JSON export.
        const tmp = document.createElement("canvas"); tmp.width = W; tmp.height = H;
        const ctx = tmp.getContext("2d"); if (!ctx) return;
        render(ctx, true);
        const picture = tmp.toDataURL("image/png");
        const markup = '<svg xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600"><image width="900" height="600" href="' + picture + '"/></svg>';
        download("safetech-camera-plan.svg", new Blob([markup], { type: "image/svg+xml" }));
    }
    async function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setSending(true); setStatus("");
        try {
            const data = new FormData();
            data.set("title", title); data.set("contact_name", name);
            data.set("contact_phone", phone); data.set("contact_email", email);
            data.set("privacy", privacy ? "1" : "0");
            data.set("layout", JSON.stringify(layout));
            if (background.startsWith("data:image/")) {
                const blob = await (await fetch(background)).blob();
                const extension = blob.type === "image/png" ? "png" : blob.type === "image/webp" ? "webp" : "jpg";
                data.set("image", blob, "floorplan." + extension);
            }
            const response = await fetch("/api/camera-plans", { method: "POST", body: data });
            if (!response.ok) throw new Error("Submission error " + response.status);
            setStatus(t.sent); setPrivacy(false);
        } catch { setStatus(t.error); }
        finally { setSending(false); }
    }

    return (
        <div className="mx-auto max-w-7xl px-4 pb-20 pt-28 text-slate-100 sm:px-8">
            <header className="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-7 shadow-2xl sm:p-12">
                <p className="mb-3 font-bold uppercase tracking-widest text-amber-400">SafeTech · CCTV Designer</p>
                <h1 className="text-3xl font-bold sm:text-5xl">{t.title}</h1>
                <p className="mt-4 max-w-3xl text-slate-300">{t.subtitle}</p>
            </header>
            <div className="mt-7 grid gap-7 xl:grid-cols-[minmax(0,1fr)_330px]">
                <section className="min-w-0 space-y-5 rounded-3xl border border-slate-700 bg-slate-900 p-4 sm:p-6">
                    <div className="flex flex-wrap gap-2">
                        <label className={button + " cursor-pointer bg-amber-500 text-slate-950"}>
                            {t.upload}<input className="sr-only" type="file" accept="image/png,image/jpeg,image/webp"
                                onChange={(e) => { void handlePhoto(e.target.files?.[0]); e.target.value = ""; }} />
                        </label>
                        <label className={button + " cursor-pointer"}>
                            {t.import}<input className="sr-only" type="file" accept=".json,application/json"
                                onChange={(e) => { void handleJSON(e.target.files?.[0]); e.target.value = ""; }} />
                        </label>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        {(["camera", "select", "wall", "area"] as Mode[]).map((item) => (
                            <button key={item} type="button" onClick={() => { setMode(item); setWallStart(null); }}
                                className={button + (mode === item ? " border-amber-400 bg-amber-500/20 text-amber-300" : "")}>
                                {t[item]}</button>
                        ))}
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <button type="button" className={button} disabled={areaDraft.length < 3}
                            onClick={() => { if (areaDraft.length < 3) return; pushHistory();
                                setLayout((prev) => ({ ...prev, area: areaDraft })); setAreaDraft([]); setMode("select"); }}>
                            {t.close}</button>
                        <button type="button" className={button} disabled={!history.length}
                            onClick={() => { const prev = history[history.length - 1]; if (!prev) return;
                                setHistory((v) => v.slice(0, -1)); setLayout(prev); setWallStart(null); setAreaDraft([]); }}>
                            {t.undo}</button>
                        <button type="button" className={button} onClick={() => {
                            if (window.confirm(t.clearConfirm)) { pushHistory(); setLayout(initial);
                                setBackground(""); setAreaDraft([]); setWallStart(null); setSelected(null); }
                        }}>{t.clear}</button>
                    </div>
                    <p className="text-sm leading-relaxed text-slate-300">{t.tip}</p>
                    <canvas ref={canvas} width={W} height={H}
                        aria-label={t.title} role="img"
                        className="w-full touch-none rounded-xl border-2 border-amber-400/40 bg-slate-50"
                        onPointerDown={pointerDown} onPointerMove={pointerMove}
                        onPointerUp={() => setDragging(null)} onPointerCancel={() => setDragging(null)}
                        style={{ aspectRatio: "3 / 2" }} />
                    <p className="text-xs text-slate-400">{t.photograph}</p>
                    <label className="block space-y-2 text-sm font-semibold">
                        <span>{t.width}: {layout.widthMeters} m</span>
                        <input className="w-full accent-amber-400" type="range" min="1" max="100" step=".5"
                            value={layout.widthMeters} onChange={(e) => setLayout((v) => ({
                                ...v, widthMeters: Number(e.target.value) }))} />
                        <input className={field} type="number" min="1" max="500" step=".5"
                            value={layout.widthMeters} onChange={(e) => setLayout((v) => ({
                                ...v, widthMeters: clamp(Number(e.target.value), 1, 500) }))} />
                    </label>
                    <div className="grid gap-3 sm:grid-cols-3">
                        <div className="rounded-2xl bg-slate-800 p-5"><p className="text-sm text-slate-400">{t.count}</p>
                            <strong className="text-3xl text-amber-400">{layout.cameras.length}</strong></div>
                        <div className="rounded-2xl bg-slate-800 p-5"><p className="text-sm text-slate-400">{t.walls}</p>
                            <strong className="text-3xl text-amber-400">{layout.walls.length}</strong></div>
                        <div className="rounded-2xl bg-slate-800 p-5"><p className="text-sm text-slate-400">{t.covered}</p>
                            <strong className="text-3xl text-amber-400">
                                {analysis.percent === null ? "—" : String(analysis.percent) + "%"}</strong></div>
                    </div>
                    <p className="text-sm text-slate-300">
                        {analysis.percent === null ? t.noArea : t.blind + ": " + String(100 - analysis.percent) + "%"}
                    </p>
                    <div className="flex flex-wrap gap-2">
                        <button type="button" className={button} onClick={exportJSON}>{t.save}</button>
                        <button type="button" className={button} onClick={exportSVG}>{t.image}</button>
                    </div>
                </section>
                <aside className="space-y-5">
                    <section className="rounded-3xl border border-slate-700 bg-slate-900 p-6">
                        <h2 className="mb-4 text-xl font-bold">{t.preview}</h2>
                        {!active ? <p className="text-slate-400">{t.camera}</p> : (
                            <div className="space-y-4">
                                <label className="block space-y-2 text-sm"><span>{t.cameraType}</span>
                                    <select className={field} value={active.kind}
                                        onChange={(e) => editCamera("kind", e.target.value)}>
                                        {kinds.map((kind) => <option key={kind} value={kind}>{kind}</option>)}
                                    </select></label>
                                {([
                                    ["direction", t.angle, 0, 360, 1],
                                    ["fov", t.fov, 15, 180, 1],
                                    ["range", t.range, 1, 100, 1],
                                    ["height", t.height, 1, 20, .1],
                                ] as [keyof Camera, string, number, number, number][]).map(([key, label, min, max, step]) => (
                                    <label className="block space-y-2 text-sm" key={key}>
                                        <span>{label}: {active[key]}</span>
                                        <input className="w-full accent-amber-400" type="range" min={min} max={max}
                                            step={step} value={Number(active[key])}
                                            onChange={(e) => editCamera(key, Number(e.target.value))} /></label>
                                ))}
                                {([
                                    ["lens", t.lens, 1, 20],
                                    ["sensor", t.sensor, 2, 20],
                                ] as [keyof Camera, string, number, number][]).map(([key, label, min, max]) => (
                                    <label className="block space-y-2 text-sm" key={key}><span>{label}</span>
                                        <input className={field} type="number" min={min} max={max} step=".1"
                                            value={Number(active[key])} onChange={(e) => {
                                                const n = clamp(Number(e.target.value), min, max);
                                                const lens = key === "lens" ? n : active.lens;
                                                const sensor = key === "sensor" ? n : active.sensor;
                                                const fov = clamp(2 * Math.atan(sensor / (2 * lens)) * 180 / Math.PI, 15, 180);
                                                pushHistory();
                                                setLayout((v) => ({ ...v, cameras: v.cameras.map((c) =>
                                                    c.id === active.id ? { ...c, [key]: n, fov } : c) }));
                                            }} /></label>
                                ))}
                                <button type="button" className={button + " w-full text-red-300"}
                                    onClick={() => { pushHistory(); setLayout((v) => ({ ...v,
                                        cameras: v.cameras.filter((c) => c.id !== active.id) })); setSelected(null); }}>
                                    {t.delete}</button>
                            </div>
                        )}
                    </section>
                    <section className="rounded-3xl border border-slate-700 bg-slate-900 p-6">
                        <h2 className="mb-4 text-xl font-bold">{t.submit}</h2>
                        <form className="space-y-4" onSubmit={(e) => { void submit(e); }}>
                            <input className={field} required maxLength={140} minLength={2}
                                placeholder={t.project} value={title} onChange={(e) => setTitle(e.target.value)} />
                            <input className={field} required maxLength={100} minLength={2}
                                placeholder={t.name} value={name} onChange={(e) => setName(e.target.value)} />
                            <input className={field} required type="tel" maxLength={24} minLength={7}
                                placeholder={t.phone} value={phone} onChange={(e) => setPhone(e.target.value)} />
                            <input className={field} type="email" maxLength={160}
                                placeholder={t.email} value={email} onChange={(e) => setEmail(e.target.value)} />
                            <label className="flex gap-3 text-sm leading-relaxed">
                                <input type="checkbox" required checked={privacy}
                                    onChange={(e) => setPrivacy(e.target.checked)} />
                                <span>{t.privacy}</span>
                            </label>
                            <button type="submit" disabled={sending || !privacy}
                                className="w-full rounded-xl bg-amber-500 px-5 py-4 font-bold text-slate-950 disabled:opacity-50">
                                {sending ? t.sending : t.submit}
                            </button>
                            <p role="status" aria-live="polite" className="text-sm text-amber-300">{status}</p>
                        </form>
                    </section>
                </aside>
            </div>
            <p className="mt-7 rounded-2xl border border-amber-500/30 bg-slate-900 p-6 text-sm leading-relaxed text-slate-300">
                {t.disclaimer}</p>
        </div>
    );
}
