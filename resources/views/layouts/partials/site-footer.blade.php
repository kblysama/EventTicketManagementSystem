<footer class="site-footer">
    <div class="site-edge footer-inner">
        <div class="footer-brand">
            <img src="{{ asset('images/yerin-logo.png') }}" alt="yerin." class="brand-logo">
            <div class="footer-copy small">
                Senin şehrin. Senin anın. Yerin hazır.<br>
                © {{ now()->year }} yerin.
            </div>
        </div>
        <div class="footer-groups">
            <div class="footer-group">
                <div class="eyebrow mb-2">Sosyal</div>
                <div class="footer-links">
                    <button class="footer-link" type="button">
                        <i class="bi bi-instagram" aria-hidden="true"></i>
                        Instagram
                    </button>
                    <button class="footer-link" type="button">
                        <i class="bi bi-tiktok" aria-hidden="true"></i>
                        TikTok
                    </button>
                    <button class="footer-link" type="button">
                        <i class="bi bi-twitter-x" aria-hidden="true"></i>
                        X
                    </button>
                </div>
            </div>
            <div class="footer-group">
                <div class="eyebrow mb-2">Yasal</div>
                <div class="footer-links">
                    <button class="footer-link" type="button">Gizlilik</button>
                    <button class="footer-link" type="button">Koşullar</button>
                </div>
            </div>
        </div>
    </div>
</footer>
