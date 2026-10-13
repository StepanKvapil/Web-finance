# Tereza Machoňová – WordPress + Vercel

Kompletní převod současného webu: 12 stránek, šest služeb, fotografie, fonty, kontaktní formuláře, ochrana údajů a animace. Administrace používá **Secure Custom Fields** (nikoli stejnojmenný plugin Smart Custom Fields).

## Co je kde
- Kořen: Next.js frontend pro Vercel. Obsah bez připojené administrace bere z `content/seed.json`.
- `wordpress/wp-content/plugins/terka-content`: vlastní plugin, SCF pole pro každou stránku, fotografie, import a REST API.
- `wordpress/wp-content/themes/terka`: šablona pro zobrazení přímo na WordPress hostingu.
- WordPress jádro, databáze a hesla nejsou verzované v repozitáři.

## WordPress administrace
1. Na PHP hostingu nainstalujte aktuální WordPress s databází a HTTPS.
2. Zkopírujte `terka-content` do `wp-content/plugins` a `terka` do `wp-content/themes` (nebo nahrajte lokální ZIP balíčky ze složky `dist-wordpress`).
3. Nainstalujte oficiální plugin **Secure Custom Fields**, aktivujte ho, poté aktivujte plugin Terka a šablonu Terka.
4. Otevřete **Obsah webu Terky → Import webu → Importovat výchozí obsah**. Import nepřepisuje již vytvořené stránky a nahraje fotografie do médií.
5. V **Obsah webu Terky** upravujte texty, obrázky a odkazy. Hlavička a patička mají vlastní záznam. Název záznamu je SEO titulek stránky.
6. API ověřte na `https://cms.vase-domena.cz/wp-json/terka/v1/page/home`.

Pole jsou zaregistrována v PHP a definována ve `schema.json`; běžný editor upravuje jejich hodnoty v administraci, strukturu mění vývojář. Opakované texty na různých stránkách mají vlastní pole. Formátovaná pole jsou pojmenována podle původního textu, odkazy podle výchozí adresy.

## Vercel
1. Importujte GitHub repozitář do Vercelu. Root Directory ponechte kořen, framework Next.js, build `npm run build`.
2. Nastavte serverovou proměnnou `WORDPRESS_URL=https://cms.vase-domena.cz` (bez koncového lomítka). Do proměnné nepatří uživatelské jméno ani heslo.
3. Nasazujte. Změny publikované ve WordPressu se projeví přibližně do 60 sekund při další návštěvě. Nový Git commit spouští nové nasazení.
4. Bez `WORDPRESS_URL` funguje úplná vložená kopie webu. Po připojení administrace se při výpadku CMS zobrazí chyba místo tichého přepnutí na staré texty.

WordPress běží samostatně na PHP hostingu; Vercel provozuje frontend. Samotný GitHub repozitář nenainstaluje WordPress ani databázi. API obsahuje pouze veřejný obsah webu, ne koncepty běžných příspěvků, uživatele nebo hesla.

## Lokálně
`npm install`, `npm run dev`, následně http://localhost:3000. Pro kontrolu `npm test` a `npm run build`. Kopii `.env.example` lze uložit jako `.env.local`.

## Kontaktní formuláře
Zachovávají původní chování: připraví e-mail v klientově e-mailovém programu. Web zprávy automaticky neposílá ani neukládá. Adresa příjemce se bere z kontaktního e-mailového odkazu upravovaného přes SCF. Přímé odesílání vyžaduje samostatné SMTP/API řešení.

## Provoz
Pravidelně aktualizujte WordPress a SCF, zálohujte databázi a média a používejte HTTPS. Text ochrany údajů je převzatý z původního webu; před produkčním provozem doplňte skutečný hosting a jeho zpracování údajů. Fotografie a fonty jsou převzaté z původního webu; ověřte oprávnění pro jejich další použití.
