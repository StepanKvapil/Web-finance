import seed from '../content/seed.json';
export async function content(slug) {
  const origin = process.env.WORDPRESS_URL?.replace(/\/$/, '');
  if (!origin) return {...seed[slug], global:seed.global};
  const response = await fetch(`${origin}/wp-json/terka/v1/page/${encodeURIComponent(slug)}`, {next:{revalidate:60}});
  if (!response.ok) throw new Error(`WordPress content request failed (${response.status})`);
  return response.json();
}
export const slugs=Object.keys(seed).filter(s=>s!=='global');
