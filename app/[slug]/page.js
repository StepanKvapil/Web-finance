import { content, slugs } from '../../lib/content';
import { notFound } from 'next/navigation';

export async function generateMetadata({ params }) {
  const p = await params;
  const slug = Array.isArray(p.slug)
    ? p.slug.join('/')
    : p.slug || 'home';

  if (!slugs.includes(slug)) return {};

  const data = await content(slug);
  return { title: data.title };
}

export default async function Page({ params }) {
  const p = await params;
  const slug = Array.isArray(p.slug)
    ? p.slug.join('/')
    : p.slug || 'home';

  if (!slugs.includes(slug)) notFound();

  const data = await content(slug);

  return (
    <>
      <a className="skip" href="#obsah">
        Přejít na obsah
      </a>
      <div className="site-shell">
        <div dangerouslySetInnerHTML={{ __html: data.global.header }} />
        <main
          id="obsah"
          dangerouslySetInnerHTML={{ __html: data.html }}
        />
        <div dangerouslySetInnerHTML={{ __html: data.global.footer }} />
      </div>
      <script src="/site.js" />
    </>
  );
}
