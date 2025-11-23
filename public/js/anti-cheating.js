/**
 * ANTI-CHEATING SYSTEM FOR PSIKOTES
 * Deteksi: Tab Switch, Screenshot, Copy-Paste, Right Click, DevTools
 * * CATATAN: Pencegahan PrintScreen (PrtSc) secara absolut tidak mungkin dilakukan 
 * di browser karena itu adalah fungsi Sistem Operasi (OS). Kode ini 
 * memaksimalkan deteksi dan mengganggu clipboard (salinan) hasil screenshot.
 */

class AntiCheatingSystem {
    constructor(testId, testResultId = null, options = {}) {
        this.testId = testId;
        this.testResultId = testResultId;
        this.violationCount = 0;
        this.maxViolations = options.maxViolations || 5;
        this.warningElement = options.warningElement || null;
        this.isBlocked = false;
        
        // CSRF Token
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        this.init();
    }

    init() {
        this.detectTabSwitch();
        this.detectScreenshot();
        this.disableCopyPaste();
        this.disableRightClick();
        this.detectDevTools();
        this.detectFullscreenExit();
        this.requestFullscreen();
        
        console.log('🔒 Anti-Cheating System Activated');
    }

    // ============================================
    // DETEKSI PINDAH TAB / WINDOW
    // ============================================
    detectTabSwitch() {
        let blurCount = 0;
        
        // Detect window blur (pindah tab/window)
        window.addEventListener('blur', () => {
            blurCount++;
            this.logViolation('TAB_SWITCH', `Pindah tab/window (ke-${blurCount})`);
            this.showWarning('⚠️ Terdeteksi pindah tab! Jangan keluar dari halaman tes!');
        });

        // Detect visibility change (tab tidak aktif)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.logViolation('WINDOW_BLUR', 'Tab tidak aktif / minimize');
                this.showWarning('⚠️ Jangan minimize atau pindah tab!');
            }
        });

        // Detect focus loss
        window.addEventListener('focusout', () => {
            this.logViolation('TAB_SWITCH', 'Focus hilang dari halaman');
        });
    }

    // ============================================
    // DETEKSI SCREENSHOT (PRINT SCREEN) - KODE INI DIPERBAIKI
    // ============================================
    // ============================================
// DETEKSI SCREENSHOT (PRINT SCREEN) - VERSI JAMIN MUNCUL PERINGATAN
// ============================================
detectScreenshot() {
    // Gunakan keydown untuk mendeteksi penekanan tombol segera
    document.addEventListener('keydown', (e) => {
        
        // 1. Tombol Print Screen (key 'PrintScreen' atau keyCode 44)
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            // *Penting*: Lakukan logging dan warning dulu, meskipun preventDefault gagal
            this.logViolation('SCREENSHOT', 'Print Screen terdeteksi');
            this.showWarning('🚫 SCREENSHOT TERDETEKSI! Ini adalah pelanggaran!');

            // Coba gagalkan aksi default (meskipun tidak efektif di level OS)
            e.preventDefault();
            e.stopPropagation();
            
            // Coba blank clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText('SCREENSHOT TIDAK DIIZINKAN')
                    .catch(err => console.warn('Clipboard write denied:', err));
            }
            return false;
        }

        // 2. Windows: Win + Shift + S (Snipping Tool)
        if (e.key === 's' && e.shiftKey && (e.metaKey || e.ctrlKey)) {
            e.preventDefault();
            this.logViolation('SCREENSHOT', 'Snipping Tool terdeteksi');
            this.showWarning('🚫 SCREENSHOT TOOL TERDETEKSI!');
            return false;
        }

        // 3. Mac: Cmd + Shift + 3/4/5
        if (e.metaKey && e.shiftKey && ['3', '4', '5'].includes(e.key)) {
            e.preventDefault();
            this.logViolation('SCREENSHOT', 'Mac screenshot terdeteksi');
            this.showWarning('🚫 SCREENSHOT TERDETEKSI!');
            return false;
        }
    });
}

        // Hapus deteksi `keyup` karena pencegahan utama sudah ada di `keydown`.
        // Hapus juga deteksi clipboard via `copy` yang cenderung menimbulkan false positive,
        // karena pencegahan clipboard sudah dilakukan di `keydown`.
    }

    // ============================================
    // DISABLE COPY-PASTE
    // ============================================
    disableCopyPaste() {
        // Disable copy
        document.addEventListener('copy', (e) => {
            e.preventDefault();
            this.logViolation('COPY_PASTE', 'Percobaan copy text');
            this.showWarning('❌ Copy text tidak diizinkan!');
            return false;
        });

        // Disable cut
        document.addEventListener('cut', (e) => {
            e.preventDefault();
            this.logViolation('COPY_PASTE', 'Percobaan cut text');
            return false;
        });

        // Disable paste
        document.addEventListener('paste', (e) => {
            e.preventDefault();
            this.logViolation('COPY_PASTE', 'Percobaan paste text');
            this.showWarning('❌ Paste tidak diizinkan!');
            return false;
        });

        // Disable select all
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                e.preventDefault();
                return false;
            }
        });
    }

    // ============================================
    // DISABLE RIGHT CLICK
    // ============================================
    disableRightClick() {
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.logViolation('RIGHT_CLICK', 'Percobaan right click');
            this.showWarning('❌ Right click tidak diizinkan!');
            return false;
        });
    }

    // ============================================
    // DETEKSI DEVELOPER TOOLS
    // ============================================
    detectDevTools() {
        // Method 1: Detect via keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // F12
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                this.logViolation('DEVELOPER_TOOLS', 'F12 pressed');
                this.showWarning('🚫 Developer Tools tidak diizinkan!');
                return false;
            }

            // Ctrl+Shift+I / Cmd+Option+I (Inspect)
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'I') {
                e.preventDefault();
                this.logViolation('DEVELOPER_TOOLS', 'Inspect Element shortcut');
                return false;
            }

            // Ctrl+Shift+J / Cmd+Option+J (Console)
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'J') {
                e.preventDefault();
                this.logViolation('DEVELOPER_TOOLS', 'Console shortcut');
                return false;
            }

            // Ctrl+Shift+C / Cmd+Option+C (Inspector)
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
                e.preventDefault();
                this.logViolation('DEVELOPER_TOOLS', 'Inspector shortcut');
                return false;
            }

            // Ctrl+U (View Source)
            if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
                e.preventDefault();
                this.logViolation('DEVELOPER_TOOLS', 'View Source');
                return false;
            }
        });

        // Method 2: Detect console opening (advanced)
        const devtoolsDetector = setInterval(() => {
            const threshold = 160;
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            
            if (widthThreshold || heightThreshold) {
                this.logViolation('DEVELOPER_TOOLS', 'DevTools window detected');
                this.showWarning('🚫 Developer Tools terdeteksi terbuka!');
            }
        }, 1000);

        // Clear interval saat window unload
        window.addEventListener('beforeunload', () => {
            clearInterval(devtoolsDetector);
        });
    }

    // ============================================
    // DETEKSI KELUAR DARI FULLSCREEN
    // ============================================
    detectFullscreenExit() {
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) {
                this.logViolation('FULLSCREEN_EXIT', 'Keluar dari mode fullscreen');
                this.showWarning('⚠️ Jangan keluar dari mode fullscreen!');
                
                // Paksa masuk fullscreen lagi setelah 2 detik
                setTimeout(() => {
                    this.requestFullscreen();
                }, 2000);
            }
        });
    }

    // ============================================
    // REQUEST FULLSCREEN MODE
    // ============================================
    requestFullscreen() {
        const elem = document.documentElement;
        
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => {
                console.warn('Fullscreen request failed:', err);
            });
        } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
            elem.msRequestFullscreen();
        }
    }

    // ============================================
    // LOG VIOLATION KE SERVER
    // ============================================
    async logViolation(violationType, description = '') {
        if (this.isBlocked) return;

        this.violationCount++;

        try {
            const response = await fetch('/api/cheating/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    test_id: this.testId,
                    test_result_id: this.testResultId,
                    violation_type: violationType,
                    description: description,
                })
            });

            const data = await response.json();

            if (data.success && data.data.is_blocked) {
                this.isBlocked = true;
                this.blockTest(data.data.warning_message);
            } else if (data.success) {
                console.warn(`⚠️ Violation: ${violationType} | Total: ${data.data.total_violations}/${data.data.max_violations}`);
            }

        } catch (error) {
            console.error('Failed to log violation:', error);
        }
    }

    // ============================================
    // SHOW WARNING MESSAGE
    // ============================================
    showWarning(message) {
        // Tampilkan di element warning jika ada
        if (this.warningElement) {
            this.warningElement.textContent = message;
            this.warningElement.style.display = 'block';
            
            setTimeout(() => {
                this.warningElement.style.display = 'none';
            }, 5000);
        }

        // Tampilkan alert
        alert(message);
    }

    // ============================================
    // BLOCK TEST (JIKA MELEBIHI BATAS)
    // ============================================
    blockTest(message) {
        // Tampilkan pesan block
        alert(message);

        // Redirect atau disable form
        const form = document.querySelector('form');
        if (form) {
            const inputs = form.querySelectorAll('input, textarea, button, select');
            inputs.forEach(input => {
                input.disabled = true;
            });
        }

        // Overlay block
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            font-size: 24px;
            text-align: center;
            padding: 20px;
        `;
        overlay.innerHTML = `
            <div>
                <h1 style="color: #ef4444; margin-bottom: 20px;">⛔ TES DIBATALKAN</h1>
                <p>${message}</p>
                <p style="margin-top: 20px; font-size: 16px;">Hubungi administrator untuk informasi lebih lanjut.</p>
            </div>
        `;
        document.body.appendChild(overlay);

        // Redirect setelah 5 detik
        setTimeout(() => {
            window.location.href = '/tests/start';
        }, 5000);
    }
}

// ============================================
// INISIALISASI
// ============================================
// Akan dipanggil dari halaman tes dengan parameter testId
window.AntiCheatingSystem = AntiCheatingSystem;