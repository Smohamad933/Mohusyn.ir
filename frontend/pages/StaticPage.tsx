import React, { useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeft, FileText } from 'lucide-react';
import { useContent } from '../context/ContentContext';

/** Generic text page (privacy, terms, support, …) — managed in the admin panel (collection: pages). */
const StaticPage: React.FC<{ slug?: string }> = ({ slug }) => {
  const params = useParams();
  const key = slug || params.slug || '';
  const navigate = useNavigate();
  const { content } = useContent();
  const page = (content.pages || []).find(p => p.slug === key);
  useEffect(() => { document.title = `${page?.title || 'Page'} – ${content.settings.fullName}`; }, [page, content.settings.fullName]);

  return (
    <div className="min-h-screen text-slate-900 dark:text-white bg-slate-50 dark:bg-black">
      <header className="sticky top-0 z-50 border-b border-slate-200 dark:border-neutral-800 bg-white/80 dark:bg-black/80 backdrop-blur-md">
        <div className="flex items-center justify-between max-w-3xl px-4 py-4 mx-auto">
          <button onClick={() => navigate('/')} className="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-neutral-400 hover:text-slate-900 dark:hover:text-white"><ArrowLeft size={16} /> Back to Home</button>
          <h1 className="flex items-center gap-2 text-lg font-bold"><FileText className="text-purple-500" size={20} /> {page?.title || 'Not found'}</h1>
          <div className="w-24" />
        </div>
      </header>
      <main className="max-w-3xl px-4 py-12 mx-auto">
        {page ? (
          <article className="prose prose-slate dark:prose-invert max-w-none [&_h2]:text-2xl [&_h2]:font-black [&_h2]:mt-10 [&_h3]:text-xl [&_h3]:font-bold [&_h3]:mt-8 [&_p]:leading-relaxed [&_p]:text-slate-600 dark:[&_p]:text-slate-300 [&_a]:text-blue-500 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:my-1 [&_img]:rounded-xl"
            dangerouslySetInnerHTML={{ __html: page.body }} />
        ) : (
          <p className="py-20 text-center text-slate-400">This page does not exist yet. Create it in the admin panel → Pages.</p>
        )}
        {page?.updatedAt && <p className="mt-12 text-xs text-slate-400">Last updated: {page.updatedAt}</p>}
      </main>
    </div>
  );
};
export default StaticPage;
