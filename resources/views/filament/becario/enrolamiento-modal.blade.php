<style>
.sigeb-fr-overlay {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);
}
.sigeb-fr-modal {
    background: #111827; border: 1px solid #374151; border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    width: 100%; max-width: 28rem; margin: 0 1rem; padding: 1.5rem;
}
.sigeb-fr-title { font-size: 1.25rem; font-weight: 700; color: #fff; margin: 0 0 0.25rem; }
.sigeb-fr-subtitle { font-size: 0.875rem; color: #9ca3af; margin: 0 0 1rem; }
.sigeb-fr-camera-wrap {
    position: relative; aspect-ratio: 3/4; background: #000;
    border-radius: 12px; overflow: hidden; margin-bottom: 1rem;
}
.sigeb-fr-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
.sigeb-fr-oval { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
.sigeb-fr-caption {
    position: absolute; bottom: 0; left: 0; right: 0; padding: 0.75rem;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    color: #fff; text-align: center;
}
.sigeb-fr-caption-main { font-weight: 600; margin: 0; }
.sigeb-fr-caption-sub { font-size: 0.75rem; color: #d1d5db; margin: 0.25rem 0 0; }
.sigeb-fr-progress { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
.sigeb-fr-progress-item { flex: 1; height: 0.5rem; border-radius: 9999px; background: #374151; }
.sigeb-fr-progress-item.done { background: #22c55e; }
.sigeb-fr-cancel {
    width: 100%; padding: 0.5rem 1rem; font-size: 0.875rem;
    color: #9ca3af; background: transparent; border: none; cursor: pointer;
}
.sigeb-fr-cancel:hover { color: #fff; }
[x-cloak] { display: none !important; }
</style>

<div
    x-data="enrolamientoFacial()"
    x-init="init()"
    x-show="visible"
    x-cloak
    class="sigeb-fr-overlay"
>
    <div class="sigeb-fr-modal">
        <h2 class="sigeb-fr-title">Registro de rostro</h2>
        <p class="sigeb-fr-subtitle">
            Es la primera vez que marcas asistencia. Necesitamos registrar tu rostro siguiendo 5 poses.
        </p>

        <div class="sigeb-fr-camera-wrap">
            <video x-ref="video" autoplay playsinline muted class="sigeb-fr-video"></video>

            <svg viewBox="0 0 300 400" class="sigeb-fr-oval" preserveAspectRatio="xMidYMid meet">
                <ellipse cx="150" cy="200" rx="110" ry="150"
                    fill="none"
                    :stroke="oval_color"
                    stroke-width="4"
                    stroke-dasharray="6 4"
                />
            </svg>

            <div class="sigeb-fr-caption">
                <p class="sigeb-fr-caption-main" x-text="instruction"></p>
                <p class="sigeb-fr-caption-sub" x-text="status"></p>
            </div>
        </div>

        <div class="sigeb-fr-progress">
            <template x-for="i in 5" :key="i">
                <div class="sigeb-fr-progress-item" :class="{ 'done': i <= captured_count }"></div>
            </template>
        </div>

        <button type="button" @click="cancel()" class="sigeb-fr-cancel">
            Cancelar
        </button>
    </div>
</div>

<script>
function enrolamientoFacial() {
    return {
        visible: @json($shouldEnroll ?? false),
        stream: null,
        captured: [],
        captured_count: 0,
        instruction: 'Inicializando cámara...',
        status: '',
        oval_color: '#ffffff',
        detectionLoop: null,
        currentStep: 0,
        steps: [
            { label: 'Mira al frente',                  check: p => Math.abs(p.yaw) < 12 && Math.abs(p.pitch) < 12 },
            { label: 'Gira ligeramente a la izquierda',   check: p => p.yaw > 12 && p.yaw < 45 },
            { label: 'Gira ligeramente a la derecha', check: p => p.yaw < -12 && p.yaw > -45 },
            { label: 'Inclina la cabeza arriba',        check: p => p.pitch < -8 && p.pitch > -35 },
            { label: 'Inclina la cabeza abajo',         check: p => p.pitch > 8 && p.pitch < 35 },
        ],
        holdFrames: 0,
        HOLD_REQUIRED: 4,
        HOLD_REQUIRED: 8,

        async init() {
            if (!this.visible) return;

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.instruction = 'Cámara no disponible';
                this.status = 'Tu navegador bloqueó el acceso a la cámara. Usa HTTPS o localhost.';
                return;
            }

            try {
                await SigebFace.loadModels();
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 640, height: 480 },
                    audio: false,
                });
                this.$refs.video.srcObject = this.stream;
                this.instruction = this.steps[0].label;
                this.status = 'Mantén el rostro dentro del óvalo';
                this.startDetection();
            } catch (e) {
                this.instruction = 'No se pudo acceder a la cámara';
                this.status = e.message;
            }
        },

        startDetection() {
            this.detectionLoop = setInterval(async () => {
                if (!this.$refs.video.videoWidth) return;

                const result = await SigebFace.detectSingle(this.$refs.video);
                if (!result) {
                    this.oval_color = '#ffffff';
                    this.status = 'No se detecta tu rostro';
                    this.holdFrames = 0;
                    return;
                }

                const pose = SigebFace.computePose(result.landmarks);
                const step = this.steps[this.currentStep];

                if (step.check(pose)) {
                    this.oval_color = '#22c55e';
                    this.holdFrames++;
                    this.status = `Manténte así... ${Math.min(this.holdFrames, this.HOLD_REQUIRED)}/${this.HOLD_REQUIRED}`;

                    if (this.holdFrames >= this.HOLD_REQUIRED) {
                        this.captured.push(Array.from(result.descriptor));
                        this.captured_count = this.captured.length;
                        this.holdFrames = 0;
                        this.currentStep++;

                        if (this.currentStep >= this.steps.length) {
                            await this.finish();
                        } else {
                            this.instruction = this.steps[this.currentStep].label;
                            this.status = 'Cambia de pose...';
                            await new Promise(r => setTimeout(r, 800));
                        }
                    }
                } else {
                    this.oval_color = '#facc15';
                    this.holdFrames = 0;
                    this.status = 'Ajusta tu pose';
                }
            }, 150);
        },

        async finish() {
            clearInterval(this.detectionLoop);
            this.instruction = 'Guardando rostro...';
            this.status = '';

            const avg = new Array(128).fill(0);
            for (const d of this.captured) {
                for (let i = 0; i < 128; i++) avg[i] += d[i];
            }
            for (let i = 0; i < 128; i++) avg[i] /= this.captured.length;

            try {
                const res = await fetch('{{ route("becario.rostro.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ descriptor: avg }),
                });

                if (!res.ok) throw new Error('Error al guardar');

                this.instruction = '¡Rostro registrado!';
                this.status = 'Ya puedes marcar tu asistencia';
                this.stopCamera();
                setTimeout(() => window.location.reload(), 1500);
            } catch (e) {
                this.instruction = 'Error al guardar';
                this.status = 'Intenta nuevamente';
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
        },

        cancel() {
            clearInterval(this.detectionLoop);
            this.stopCamera();
            this.visible = false;
        },
    };
}
</script>