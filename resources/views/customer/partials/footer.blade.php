{{-- Partial footer customer: wave + kolom footer. Include di akhir setiap halaman customer. --}}
<style>
    footer { margin:0; padding:72px 7% 32px; color:#fff; background:var(--green, #0b412b); display:grid; grid-template-columns:1.1fr 1fr 1fr; gap:25px; }
    .wave { display:block; width:100%; height:110px; margin:48px 0 0; }
    footer .brand { filter:brightness(0) invert(1); width:150px; margin-bottom:9px; }
    footer h4 { margin:0 0 8px; font-size:15px; }
    footer p { font-size:12px; line-height:1.25; margin:0; }
    .map { height:115px; background:linear-gradient(135deg,#e8e1d0,#c6d5c1); border:5px solid white; color:#55836b; display:grid; place-items:center; font-size:12px; }
    @media (max-width:700px) { footer{padding:48px 25px 28px;grid-template-columns:1fr 1fr;gap:25px 15px}.wave{height:55px;margin-top:28px}footer>div:first-child{grid-column:1/-1}footer .brand{width:120px}.map{height:85px} }
</style>
<svg class="wave" viewBox="0 0 1440 320" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path fill="#0b412b" d="M0,160L48,149.3C96,139,192,117,288,96C384,75,480,53,576,53.3C672,53,768,75,864,101.3C960,128,1056,160,1152,149.3C1248,139,1344,85,1392,58.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/></svg>
<footer><div><img class="brand" src="{{ asset('images/hamta-logo-nobg.svg') }}" alt="Hamta"><p>Eat Good, Feel Good</p></div><div><h4>Lokasi Kita</h4><p>Lidah Kulon<br>Rumah Produksi,<br>Jl. Lidah Kulon Gg. 1, Lidah Kulon,<br>Kec. Lakarsantri,<br>Surabaya, Jawa Timur<br>60213</p></div><div><div class="map">Lidah Kulon • Surabaya</div></div><div><h4>Our Sosmed</h4></div><div><h4>Contact Us</h4></div></footer>
