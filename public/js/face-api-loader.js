(function () {
    if (window.SigebFace) return;

    const MODELS_URL = '/models';
    const SCRIPT_URL = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js';

    let scriptPromise = null;
    let modelsPromise = null;

    function loadScript() {
        if (scriptPromise) return scriptPromise;
        scriptPromise = new Promise((resolve, reject) => {
            if (window.faceapi) return resolve();
            const s = document.createElement('script');
            s.src = SCRIPT_URL;
            s.defer = true;
            s.onload = () => resolve();
            s.onerror = () => reject(new Error('No se pudo cargar face-api.js'));
            document.head.appendChild(s);
        });
        return scriptPromise;
    }

    async function loadModels() {
        if (modelsPromise) return modelsPromise;
        modelsPromise = (async () => {
            await loadScript();
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri(MODELS_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODELS_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
            ]);
        })();
        return modelsPromise;
    }

    async function detectSingle(input) {
        await loadModels();
        return faceapi
            .detectSingleFace(input, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.6 }))
            .withFaceLandmarks()
            .withFaceDescriptor();
    }

    function euclideanDistance(a, b) {
        return faceapi.euclideanDistance(a, b);
    }

    function computePose(landmarks) {
        const pts = landmarks.positions;
        const noseTip = pts[30];
        const leftEye = pts[36];
        const rightEye = pts[45];
        const chin = pts[8];
        const foreheadCenter = {
            x: (leftEye.x + rightEye.x) / 2,
            y: (leftEye.y + rightEye.y) / 2,
        };

        const eyeMid = foreheadCenter;
        const faceWidth = rightEye.x - leftEye.x;
        const yaw = ((noseTip.x - eyeMid.x) / faceWidth) * 90;

        const faceHeight = chin.y - eyeMid.y;
        const pitch = ((noseTip.y - eyeMid.y) / faceHeight - 0.5) * 90;

        return { yaw, pitch };
    }

    window.SigebFace = {
        loadModels,
        detectSingle,
        euclideanDistance,
        computePose,
        MATCH_THRESHOLD: 0.5,
    };
})();