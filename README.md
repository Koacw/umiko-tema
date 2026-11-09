# umiko · WooCommerce teması

**Canlı önizleme (tarayıcıda, kurulum gerekmez):**
https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/Koacw/umiko-tema/main/blueprint.json

Link WordPress Playground'da WooCommerce'i, bu temayı ve örnek ürünleri birkaç dakikada kurar. Sepet ve ödeme (kapıda ödeme, demo) çalışır. Her açılışta sıfırdan kurulur ve yapılan değişiklikler kaydedilmez. Tema her zaman bu deponun `main` dalından gelir, yani GitHub'a gönderilen her değişiklik linkte de görünür.

gereksiz ama gerekli. umiko.shop için yazılmış WordPress + WooCommerce teması.

```
umiko/                    ← tema klasörü (WordPress'e yüklenecek olan bu)
  functions.php           ← dosyaları yükler
  theme.json              ← renk ve font token'ları (editör paleti)
  inc/
    checkout-mode.php     ← satın alma davranışı tek yerden (native | external)
    product-meta.php      ← ürün düzenleme ekranındaki "umiko" sekmesi
    customizer.php        ← Görünüm > Özelleştir > umiko
    demo-import.php       ← Ürünler > umiko örnek ürünler
    pages.php, seo.php, setup.php, helpers.php
  demo/products.php       ← ürün kataloğu (12 ürün, tek veri dosyası)
  woocommerce/            ← mağaza, ürün kartı, ürün detay şablonları
  template-parts/         ← kart, sepet çekmecesi, kulüp, satıcı bilgileri, çerez bandı
  front-page.php, page-hikaye.php, page-sss.php, page-iletisim.php,
  page-templates/legal.php, 404.php
  assets/css/umiko.css    ← bütün stiller (build adımı yok)
  assets/js/umiko.js      ← ES module, jQuery yok
  assets/brand, fonts, img
blueprint.json            ← canlı önizleme linki (temayı GitHub'dan kurar)
blueprint-local.json      ← bu bilgisayarda önizleme (temayı klasörden bağlar)
umiko.zip                 ← WordPress'e yüklenecek hazır paket (depoda değil, GitHub Releases sayfasında)
```

## Neler var

- **Satış modu tek ayardan:** Özelleştir > umiko > satış modu.
  - *WooCommerce sepeti* (şu anki varsayılan): kartlarda ve ürün sayfasında renk seçimi + "sepete at", sayfa yenilenmeden açılan sepet çekmecesi (WooCommerce Store API), sonra WooCommerce ödeme sayfası.
  - *Vitrin (Faz 1)*: sepet ve ödeme kapanır; "shopier'den al" ve varsa "etsy'de gör" butonları yeni sekmede açılır. Şablonlar değişmez, sadece butonlar değişir.
- **Ürün eklemek kod istemez:** Ürünler > Yeni ekle. Renkler = "renk" özniteliği + varyasyonlar. Shopier/Etsy linki, hazırlık süresi, malzeme, bakım notu, rozet ve "gereksiz / gerekli" cümleleri ürün verilerindeki **umiko** sekmesinde. Ölçüler WooCommerce'in *Gönderim* sekmesinde.
- **Comic tasarım:** mürekkep konturlu kartlar ve butonlar, sert "pop" gölge (basınca gölgeye gömülür), halftone nokta dokusu, konuşma balonları, eğik çıkartma rozetler, "hop!" gibi ses efekti yazıları. Ana sayfa hero'su eğik polaroid comic karelerden oluşan bir kolaj; "gereksiz ama gerekli" bölümü konuşma balonlu comic şeritler.
- **Kullanım çizimleri:** `assets/illustrations/` içinde 8 SVG sahne (mama sıyırıcı, çöp poşeti aparatı, kumandalık, masa, ev, kedi, atölye, kedi yüzü). Marka renklerini CSS token'larından alırlar. Ürün düzenle > umiko > *Comic çizim* ile her ürüne bir sahne, *Çizim kadrajı* ile sahnenin bir bölümü (ör. masada sadece kulaklık askısı) seçilir.
- **Fotoğrafsız ürünler** çizimleriyle ve "çizim · fotoğraf gelecek" etiketiyle görünür. Ürün görseli yüklenince fotoğraf kendiliğinden çizimin yerini alır.
- **Modern teknikler:** CSS view transitions (karttaki görsel ürün sayfasına geçer), scroll-driven animasyonlar, `@starting-style` ile açılan `<dialog>` çekmeceler, `::details-content` ile açılan akordiyonlar, container query'ler, `linear()` zıplama eğrisi, `:has()`, WordPress script modules (WP 6.7+), font dosyaları temanın içinde (Google'a istek yok). `prefers-reduced-motion` açıksa bütün hareketler kapanır.
- **Erişilebilirlik:** `lang="tr"`, içeriğe geç linki, mavi odak halkası, klavyeyle kullanılabilen menü, sepet ve anahtarlar, ekran okuyucu duyuruları ("sepete eklendi"). Pembe hiçbir yerde metin rengi değil.
- **SEO:** her sayfaya açıklama ve Open Graph etiketi, ana sayfada Organization şeması, ürünlerde WooCommerce'in Product şeması (TRY). `sitemap` ve `robots.txt` WordPress çekirdeğinden gelir (`/wp-sitemap.xml`). Yoast ya da Rank Math kurulursa tema kendi etiketlerini basmayı bırakır.
- **Çerezler:** bilgilendirme bandı. WooCommerce'in sipariş kaynağı takibi (`sbjs_*` çerezleri) kapalı. İleride analitik eklenirse `window.umikoConsent.onGrant(() => …)` içinde yüklenmeli.

## Yerelde açmak (önizleme)

Node.js yeterli; PHP ya da Docker gerekmez.

```bash
npx @wp-playground/cli@3.1.57 server --port=9400 --mount-dir ./umiko /wordpress/wp-content/themes/umiko --blueprint=./blueprint-local.json --login
```

Git Bash kullanıyorsan komutun başına `MSYS_NO_PATHCONV=1` ekle. Sonra http://127.0.0.1:9400 adresini aç. WooCommerce, Türkçe dil, ₺ fiyat biçimi, 12 ürün ve bütün sayfalar kendiliğinden kurulur. Ödeme yöntemi olarak demo amaçlı "Kapıda ödeme" açılır. Bu site geçicidir; sunucuyu kapatınca silinir.

## [DOLDUR] listesi

Hepsi sitede pembe-krem kesik çizgili `[DOLDUR: …]` etiketi olarak görünür. Çoğu kod gerektirmez.

**Özelleştir > umiko** (WordPress paneli, kod yok)

| Ne | Nerede görünür | Kod tarafı |
|---|---|---|
| Instagram kullanıcı adı | footer, kulüp bölümü, iletişim | `footer.php:24`, `template-parts/club.php:33`, `page-iletisim.php:36` |
| Kurucunun hikâyesi | ana sayfa atölye bölümü, hikâye sayfası | `front-page.php:144`, `page-hikaye.php:27` |
| E-posta listesi form adresi (Brevo, Mailchimp…) | kulüp bölümü (boşken form gizli) | `template-parts/club.php` |
| Satıcı: ad soyad / unvan | footer, iletişim, yasal sayfalar | `template-parts/seller-info.php:12` |
| Satıcı: adres | 〃 | `template-parts/seller-info.php:13` |
| Satıcı: e-posta | 〃 + iletişim kartı | `template-parts/seller-info.php:14`, `page-iletisim.php:27` |
| Satıcı: telefon | 〃 | `template-parts/seller-info.php:15` |
| Satıcı: vergi no / MERSİS no | 〃 | `template-parts/seller-info.php:16` |
| ETBİS doğrulama kodu (Faz 2) | footer'daki ayrılmış alan | `footer.php:46` |

**Ürünler** (WooCommerce > ürünü düzenle; `demo/products.php` başlangıç verisi)

| Ne | Kod tarafı |
|---|---|
| 3 lansman ürününün gerçek fiyatı (şu an 129,90 / 189,90 / 219,90 TL örnek) | `demo/products.php:28, 44, 60` |
| Kesin renkler (şu an pembe, mavi) | `demo/products.php:29, 45, 61` |
| Hazırlık süresi: mama sıyırıcı teyidi, çöp poşeti aparatı boş, kumandalık teyidi | `demo/products.php:35, 51, 67` → sitede `woocommerce/single-product.php:95` |
| Ölçüler (3 lansman ürünü) | Gönderim sekmesi → sitede `woocommerce/single-product.php:113` |
| Shopier linkleri (vitrin modunda gerekli) | umiko sekmesi → sitede `inc/checkout-mode.php:160` |
| Ürün fotoğrafları | ürün görseli + galeri |
| 9 örnek ürün uydurmadır: yayına almadan **Ürünler > umiko örnek ürünler > örnek ürünleri sil** | `demo/products.php` (`seed: ornek`) |

**Metinler**

| Ne | Kod tarafı |
|---|---|
| SSS cevapları (kargo, hazırlık süresi, iade, bakım, siparişe özel üretim) | `page-sss.php:12–16` (ya da SSS sayfasının editörüne yaz) |
| 6 yasal metin (hukukçu / mali müşavir hazırlayacak) | ilgili sayfanın editörüne yapıştırılır; kutu kendiliğinden kalkar: `page-templates/legal.php:27` |
| Yasal sayfaların "son güncelleme" tarihi | metin eklenince otomatik: `page-templates/legal.php:21` |
| Logo SVG sürümü (varsa) | şu an PNG kullanılıyor: `assets/brand/` |

## Yayına alma (umiko.shop)

1. **Hosting:** WordPress çalıştıran bir hosting al (PHP 8.1+, MySQL, SSL dahil olsun). Panelden tek tıkla WordPress kur, dili **Türkçe** seç.
2. **Alan adı:** umiko.shop'un DNS kayıtlarını hosting firmasının verdiği adrese yönlendir, SSL'i (Let's Encrypt) aç. Ayarlar > Genel'de adresin `https://umiko.shop` olduğundan emin ol.
3. **Kalıcı bağlantılar:** Ayarlar > Kalıcı bağlantılar > *Yazı adı*.
4. **WooCommerce:** Eklentiler > Yeni ekle > WooCommerce'i kur ve etkinleştir. Kurulum sihirbazında ülke Türkiye, para birimi Türk lirası. WooCommerce > Ayarlar > Genel'de binlik ayırıcı `.`, ondalık ayırıcı `,`, ondalık sayısı `2`, para birimi konumu *sol* → `₺349,90`. Vergi sekmesinde "fiyatlar KDV dahil girilecek".
5. **Tema:** Görünüm > Temalar > Yeni ekle > Tema yükle > `umiko.zip` > Etkinleştir. Hikâye, SSS, iletişim ve 6 yasal sayfa kendiliğinden oluşur (eksik kalırsa Görünüm > umiko sayfaları).
6. **Özelleştir > umiko:** satış modunu seç, Instagram'ı, hikâyeyi ve satıcı bilgilerini doldur.
7. **Ürünler:** Ürünler > umiko örnek ürünler > *sadece 3 lansman ürününü yükle*. Fiyatları, ölçüleri ve fotoğrafları düzelt. Ya da ürünleri doğrudan elle ekle.
8. **Ödeme (Faz 2):** şirket kurulunca iyzico ya da PayTR'nin resmi WooCommerce eklentisini kur ve anahtarlarını gir; Stripe Türkiye'de çalışmıyor. O zamana kadar satış modunu *Vitrin* yap ve ürünlere Shopier linklerini gir.
9. **Son kontrol:** sitede hiç `[DOLDUR` kalmadığını kontrol et, yasal metinleri ekle, telefonla (Instagram'dan gelen trafik gibi) bir tur gez.
