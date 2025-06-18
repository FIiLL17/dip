 const songs = [
            { id: "audio1", title: "Je te laisserai des mots (Slowed + Rain)", artist: "IRSNa, Paré", src: "IRSNa_Par_-_Je_te_laisserai_des_mots_Slowed_Rain_77885159 (1).mp3" },
            { id: "audio2", title: "Mercy", artist: "Dotan", src: "Dotan_-_Mercy_72911179.mp3" },
            { id: "audio3", title: "AGARA", artist: "GOOS", src: "GOOS_-_AGARA_78387241.mp3" },
            { id: "audio4", title: "Till the end", artist: "Genetekk", src: "Genetekk — Till the end [ HARDTEKK ] [BASS BOOSTED] (www.lightaudio.ru).mp3" }
        ];

        const audioElements = songs.map(song => {
            const audio = document.getElementById(song.id);
            audio.loop = true; // Устанавливаем loop для всех аудио
            return audio;
        });
        
        const pauseButton = document.getElementById("pauseButton");
        const nextButton = document.getElementById("nextButton");
        const prevButton = document.getElementById("prevButton");
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');

        let currentSongIndex = 0;
        let isPlaying = false;
        let audioContext;
        let analyser;
        let source;
        let bufferLength;
        let dataArray;
        let animationFrame = 0;
        let startDelay = [];
        let smoothedBarHeight = [];
        let isInitialized = false; // Флаг инициализации

        function setupVisualizer() {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            analyser = audioContext.createAnalyser();
            analyser.fftSize = 1024;
            bufferLength = analyser.frequencyBinCount;
            dataArray = new Uint8Array(bufferLength);

            canvas.width = 1900;
            canvas.height = 900;

            source = audioContext.createMediaElementSource(audioElements[currentSongIndex]);
            source.connect(analyser);
            analyser.connect(audioContext.destination);
            startDelay = Array(bufferLength).fill(0);
            smoothedBarHeight = Array(bufferLength).fill(0);
        }

        function draw() {
            animationFrame = requestAnimationFrame(draw);

            analyser.getByteFrequencyData(dataArray);

            ctx.fillStyle = '#050510';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const numPoints = 384;
            const centerY = canvas.height / 2;

            animationFrame++;

            // Градиент
            const gradient = ctx.createLinearGradient(0, centerY - 150, 0, centerY + 150);
            gradient.addColorStop(0, 'DodgerBlue');
            gradient.addColorStop(1, 'MediumVioletRed');
            ctx.fillStyle = gradient;

            const bassMultiplier = 2.0;
            const trebleMultiplier = 0.75;
            const smoothingFactor = 0.15; // Фактор сглаживания
            const xOffsetAmplitude = 3;  // Амплитуда смещения по X
            const xOffsetSpeed = 50; // Скорость смещения

            for (let i = 0; i < numPoints; i++) {
                // Индекс текущей частоты
                const frequencyIndex = Math.floor((i / numPoints) * bufferLength);

                // Height
                let barHeight = dataArray[frequencyIndex] * 0.5;

                // Усиление басов и высоких частот
                if (frequencyIndex < bufferLength / 3) {
                    barHeight *= bassMultiplier;  // Усиление басов
                } else if (frequencyIndex > 2 * bufferLength / 3) {
                    barHeight *= trebleMultiplier;  // Усиление высоких частот
                }

                // Плавное изменение высоты столбиков
                smoothedBarHeight[frequencyIndex] += (barHeight - smoothedBarHeight[frequencyIndex]) * smoothingFactor;
                barHeight = smoothedBarHeight[frequencyIndex];

                // Реализация "вырастания"
                if (startDelay[frequencyIndex] < 20) {
                    startDelay[frequencyIndex]++;
                    const normalizedDelay = startDelay[frequencyIndex] / 20;
                    barHeight = barHeight * normalizedDelay * normalizedDelay * normalizedDelay; //  экспоненциальное сглаживание
                }

                //Смещение по X, чтобы волны как бы пересекались
                const xOffset = Math.sin(animationFrame / xOffsetSpeed + i * 0.1) * xOffsetAmplitude;
                // Отрисовка оригинального эквалайзера
                let x = (i / numPoints) * canvas.width + xOffset;
                let y = centerY - barHeight;
                const width = 2;

                // Свечение
                ctx.shadowColor = ctx.fillStyle;
                ctx.shadowBlur = 5;
                ctx.fillRect(x, y, width, barHeight * 1.5);
                ctx.shadowBlur = 0;

                // Отрисовка отраженного эквалайзера
                const mirroredX = canvas.width - x - width + 2 * xOffset;
                ctx.shadowColor = ctx.fillStyle;
                ctx.shadowBlur = 5;
                ctx.fillRect(mirroredX, y, width, barHeight * 1.5);
                ctx.shadowBlur = 0;
            }
        }

       function initializePlayer() {
            if (!isInitialized) {
                setupVisualizer();
                draw();
                isInitialized = true;
            }
        }

        function playSong(index) {
            if (index < 0 || index >= songs.length) return;

            initializePlayer(); // Инициализируем при первом воспроизведении

            const audio = audioElements[index];
            currentSongIndex = index;

            audioElements.forEach((el, i) => {
                if (i !== index) {
                    el.pause();
                    el.currentTime = 0;
                }
            });

            if (audioContext && source) {
                source.disconnect();
                source = audioContext.createMediaElementSource(audio);
                source.connect(analyser);
                analyser.connect(audioContext.destination);
            }

            audio.play()
                .then(() => {
                    isPlaying = true;
                    document.querySelector('.vinyl').style.animationPlayState = 'running';
                    pauseButton.textContent = "⏸";
                })
                .catch(error => {
                    console.error("Playback failed:", error);
                });
        }

        function togglePause() {
            if (!isInitialized) {
                playSong(currentSongIndex);
                return;
            }

            const audio = audioElements[currentSongIndex];
            
            if (isPlaying) {
                audio.pause();
                isPlaying = false;
                document.querySelector('.vinyl').style.animationPlayState = 'paused';
                pauseButton.textContent = "▶";
            } else {
                audio.play()
                    .then(() => {
                        isPlaying = true;
                        document.querySelector('.vinyl').style.animationPlayState = 'running';
                        pauseButton.textContent = "⏸";
                    })
                    .catch(error => {
                        console.error("Playback failed:", error);
                    });
            }
        }

        function nextSong() {
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            playSong(currentSongIndex);
        }

        function prevSong() {
            currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
            playSong(currentSongIndex);
        }

        pauseButton.addEventListener("click", togglePause);
        nextButton.addEventListener("click", nextSong);
        prevButton.addEventListener("click", prevSong);

        // Обработчики для автоматического воспроизведения после взаимодействия
        document.addEventListener('click', function initOnClick() {
            if (!isInitialized) {
                initializePlayer();
                playSong(currentSongIndex);
                document.removeEventListener('click', initOnClick);
            }
        }, { once: true });

        // Инициализация при загрузке (но воспроизведение начнется только после взаимодействия)
        window.addEventListener('load', function() {
            initializePlayer();
        });