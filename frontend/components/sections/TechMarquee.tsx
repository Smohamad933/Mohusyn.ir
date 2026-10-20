import React from 'react';
import { Sparkles } from 'lucide-react';
import { useSettings } from '../../context/ContentContext';

const TechMarquee: React.FC = () => {
  const st = useSettings();
  const COLORS = ['text-yellow-400', 'text-blue-400', 'text-cyan-400', 'text-green-500', 'text-teal-400', 'text-purple-400', 'text-blue-500', 'text-white', 'text-pink-400'];
  const techs = (st.stack || []).map((t, i) => ({ name: t.name, icon: t.icon ? <span className="text-xl leading-none">{t.icon}</span> : <Sparkles size={24} />, color: COLORS[i % COLORS.length] }));
  if (!techs.length) return null;

  return (
    <section className="relative py-8 overflow-hidden bg-black border-y border-zinc-900">
      <div className="absolute inset-0 z-10 pointer-events-none bg-gradient-to-r from-black via-transparent to-black"></div>
      
      <div className="container relative z-20 px-4 mx-auto mb-4">
        <p className="text-xs font-bold text-zinc-500 uppercase tracking-[0.2em] text-center">{st.sectionTitles.marquee?.title || "Powering My Workflow"}</p>
        </div>

      <div className="relative flex overflow-x-hidden group">
        <div className="flex items-center gap-12 px-6 animate-marquee whitespace-nowrap">
          {/* Triple the list for seamless loop on wide screens */}
          {[...techs, ...techs, ...techs].map((tech, index) => (
            <div 
              key={index} 
              className="flex items-center gap-3 transition-all duration-300 cursor-default group/item opacity-60 hover:opacity-100"
            >
              <span className={`p-2.5 bg-zinc-900 rounded-xl border border-zinc-800 shadow-sm group-hover/item:border-zinc-700 transition-colors grayscale group-hover/item:grayscale-0 ${tech.color}`}>
                {tech.icon}
              </span>
              <span className="text-lg font-bold transition-colors text-zinc-400 group-hover/item:text-white">{tech.name}</span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default TechMarquee;