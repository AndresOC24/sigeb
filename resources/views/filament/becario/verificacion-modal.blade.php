<style>
.sigeb-vf-overlay {
    position: fixed; inset: 0; z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);
}
.sigeb-vf-modal {
    background: #111827; border: 1px solid #374151; border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    width: 100%; max-width: 28rem; margin: 0 1rem; padding: 1.5rem;
}
.sigeb-vf-title { font-size: 1.25rem; font-weight: 700; color: #fff; margin: 0 0 0.25rem; }
.sigeb-vf-subtitle { font-size: 0.875rem; color: #9ca3af; margin: 0 0 1rem; }
.sigeb-vf-camera-wrap {
    position: relative; aspect-ratio: 3/4; background: #000;
    border-radius: 12px; overflow: hidden; margin-bottom: 1rem;
}
.sigeb-vf-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
.sigeb-vf-oval { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; }
.sigeb-vf-caption {
    position: absolute; bottom: 0; left: 0; right: 0; padding: 0.75rem;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    color: #fff; text-align: center;
}
.sigeb-vf-caption-main { font-weight: 600; margin: 0; }
.sigeb-vf-caption-sub { font-size: 0.75rem; color: #d1d5db; margin: 0.25rem 0 0; }
.sigeb-vf-textarea-wrap { margin-bottom: 1rem; }
.sigeb-vf-textarea-label { display: block; font-size: 0.875rem; color: #d1d5db; margin-bottom: 0.5rem; font-weight: 500; }
.sigeb-vf-textarea {
    width: 100%; min-height: 100px; padding: 0.625rem 0.75rem;
    background: #1f2937; border: 1px solid #374151; border-radius: 8px;
    color: #fff; font-family: inherit; font-size: 0.875rem; resize: vertical;
}
.sigeb-vf-textarea:focus { outline: none; border-color: #3b82f6; }
.sigeb-vf-buttons { display: flex; gap: 0.5rem; }
.sigeb-vf-btn {
    flex: 1; padding: 0.625rem 1rem; font-size: 0.875rem; font-weight: 500;
    border-radius: 8px; border: none; cursor: pointer; transition: opacity 0.15s;
}
.sigeb-vf-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.sigeb-vf-btn-primary { background: #3b82f6; color: #fff; }
.sigeb-vf-btn-primary:hover:not(:disabled) { background: #2563eb; }
.sigeb-vf-btn-cancel { background: transparent; color: #9ca3af; }
.sigeb-vf-btn-cancel:hover { color: #fff; }
.sigeb-vf-error { color: #ef4444; font-size: 0.875rem; text-align: center; margin: 0 0 0.5rem; }
.sigeb-vf-attempts { font-size: 0.75rem; color: #9ca3af; text-align: center; margin: 0 0 0.5rem; }
[x-cloak] { display: none !important; }
</style>

<div
    x-data="verificacionFacial()"
    x-show="visible"
    x-cloak
    class="sigeb-vf-overlay"
    @open-verificacion.window="open($event.detail)"
>
    <div class="sigeb-vf-modal">
        <h2 class="sigeb-vf-title" x-text="tipo === 'entrada' ? 'Marcar Entrada' : 'Marcar Salida'"></h2>
        <p class="sigeb-vf-subtitle">Verifica tu identidad siguiendo las indicaciones.</p>

        <div class="sigeb-vf-camera-wrap">
            <video x-ref="video" autoplay playsinline muted class="sigeb-vf-video"></video>
            <svg viewBox="0 0 300 400" class="sigeb-vf-oval" preserveAspectRatio="xMidYMid meet">
                <ellipse cx="150" cy="200" rx="110" ry="150"
                    fill="none"
                    :stroke="oval_color"
                    stroke-width="4"
                    stroke-dasharray="6 4"
                />
            </svg>
            <div class="sigeb-vf-caption">
                <p class="sigeb-vf-caption-main" x-text="instruction"></p>
                <p class="sigeb-vf-caption-sub" x-text="status"></p>
            </div>
        </div>

        <p class="sigeb-vf-attempts" x-show="attempts > 0" x-text="`Intentos restantes: ${MAX_ATTEMPTS - attempts}`"></p>
        <p class="sigeb-vf-error" x-show="errorMsg" x-text="errorMsg"></p>

        <div class="sigeb-vf-textarea-wrap" x-show="tipo === 'salida'">
            <label class="sigeb-vf-textarea-label">Actividad principal</label>
            <textarea
                x-model="actividad"
                class="sigeb-vf-textarea"
                placeholder="Ej: Soporte técnico a docentes, instalación de software en laboratorio 3..."
                minlength="10"
                maxlength="2000"
            ></textarea>
        </div>

        <div class="sigeb-vf-buttons">
            <button type="button" @click="cancel()" class="sigeb-vf-btn sigeb-vf-btn-cancel">Cancelar</button>
        </div>
    </div>
</div>

<script>
function verificacionFacial() {
    return {
        visible: false,
        tipo: 'entrada',
        actividad: '',
        stream: null,
        instruction: 'Inicializando cámara...',
        status: '',
        oval_color: '#ffffff',
        errorMsg: '',
        detectionLoop: null,
        currentStep: 0,
        attempts: 0,
        MAX_ATTEMPTS: 3,
        HOLD_REQUIRED: 3,
        holdFrames: 0,
        capturedDescriptor: null,
        steps: [],

        buildSteps() {
            const gesture = Math.random() < 0.5
                ? { label: 'Gira ligeramente a la izquierda', check: p => p.yaw > 12 && p.yaw < 45 }
                : { label: 'Gira ligeramente a la derecha', check: p => p.yaw < -12 && p.yaw > -45 };

            this.steps = [
                { label: 'Mira al frente', check: p => Math.abs(p.yaw) < 12 && Math.abs(p.pitch) < 12, capture: true },
                gesture,
            ];
        },

        async open(detail) {
            this.tipo = detail.tipo;
            this.actividad = '';
            this.errorMsg = '';
            this.attempts = 0;
            this.visible = true;
            await this.$nextTick();
            await this.start();
        },

        async start() {
            this.currentStep = 0;
            this.holdFrames = 0;
            this.capturedDescriptor = null;
            this.buildSteps();

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.instruction = 'Cámara no disponible';
                this.status = 'Usa HTTPS o localhost.';
                return;
            }

            try {
                await SigebFace.loadModels();
                if (!this.stream) {
                    this.stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: 640, height: 480 },
                        audio: false,
                    });
                    this.$refs.video.srcObject = this.stream;
                }
                this.instruction = this.steps[0].label;
                this.status = 'Mantén el rostro dentro del óvalo';
                this.startDetection();
            } catch (e) {
                this.instruction = 'No se pudo acceder a la cámara';
                this.status = e.message;
            }
        },

        startDetection() {
            clearInterval(this.detectionLoop);
            this.detectionLoop = setInterval(async () => {
                if (!this.$refs.video || !this.$refs.video.videoWidth) return;

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
                        if (step.capture) {
                            this.capturedDescriptor = Array.from(result.descriptor);
                        }
                        this.holdFrames = 0;
                        this.currentStep++;

                        if (this.currentStep >= this.steps.length) {
                            await this.submit();
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

        async submit() {
            clearInterval(this.detectionLoop);

            if (this.tipo === 'salida' && this.actividad.trim().length < 10) {
                this.errorMsg = 'Describe la actividad principal (mínimo 10 caracteres).';
                this.instruction = 'Completa el formulario';
                this.status = '';
                return;
            }

            this.instruction = 'Verificando identidad...';
            this.status = '';

            try {
                const res = await fetch('{{ route("becario.asistencia.verificar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        descriptor: this.capturedDescriptor,
                        tipo: this.tipo,
                        actividad_principal: this.tipo === 'salida' ? this.actividad : null,
                    }),
                });

                const json = await res.json();

                if (!res.ok || !json.ok) {
                    this.attempts++;
                    if (this.attempts >= this.MAX_ATTEMPTS) {
                        this.instruction = 'Verificación fallida';
                        this.status = 'Contacta al Encargado General para marcar manualmente.';
                        this.errorMsg = json.message || 'No se pudo verificar.';
                        this.stopCamera();
                        return;
                    }
                    this.errorMsg = (json.message || 'Verificación fallida') + '. Intenta nuevamente.';
                    await this.start();
                    return;
                }

                this.instruction = '¡Verificado!';
                this.status = json.message;
                this.errorMsg = '';
                this.stopCamera();
                setTimeout(() => window.location.reload(), 1200);
            } catch (e) {
                this.errorMsg = 'Error de red. Intenta nuevamente.';
                await this.start();
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            clearInterval(this.detectionLoop);
        },

        cancel() {
            this.stopCamera();
            this.visible = false;
        },
    };
}
</script>