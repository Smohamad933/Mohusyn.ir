import React, { useState, useRef, useEffect, MouseEvent } from 'react';
import { useContent } from '../../context/ContentContext';
import { ytEmbed } from '../../lib/utils';
import { ChevronRight, X, Copy, Check, Terminal, Play, Eye, ExternalLink } from 'lucide-react';
import Tilt3D from '../ui/Tilt3D';

const Projects: React.FC = () => {
  const [showCodeModal, setShowCodeModal] = useState(false);
  const [activeCode, setActiveCode] = useState(""); 
  const [activeFilename, setActiveFilename] = useState(""); 
  const [copied, setCopied] = useState(false);
  const codeRef = useRef<HTMLDivElement>(null);

  const [showVideoModal, setShowVideoModal] = useState(false);
  const [activeVideo, setActiveVideo] = useState('');
  const { content } = useContent();
  const titles = content.settings.sectionTitles.projects;
  const allProjects = content.projects.filter(p => p.published !== false);
  const flagship = allProjects.find(p => p.featured);
  const gridProjects = allProjects.filter(p => !p.featured);
  const _unusedLink = "https://www.facebook.com/rhythm2OfPeace";

  // ---------------------------------------------------------
  // 💡 Spotlight & Subtle Tilt Effect Logic for Flagship Card
  // ---------------------------------------------------------
  const flagshipRef = useRef<HTMLDivElement>(null);
  const [isFocused, setIsFocused] = useState(false);
  const [position, setPosition] = useState({ x: 0, y: 0 });
  const [tilt, setTilt] = useState({ x: 0, y: 0 });

  const handleMouseMove = (e: MouseEvent<HTMLDivElement>) => {
    if (!flagshipRef.current) return;
    const rect = flagshipRef.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    setPosition({ x, y });

    // অত্যন্ত স্মুথ এবং সামান্য টিল্ট ক্যালকুলেশন (Max 1.5 degrees)
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const tiltX = -((y - centerY) / centerY) * 1.5;
    const tiltY = ((x - centerX) / centerX) * 1.5;
    setTilt({ x: tiltX, y: tiltY });
  };

  const handleMouseLeave = () => {
    setIsFocused(false);
    setTilt({ x: 0, y: 0 }); // মাউস সরালে স্মুথলি আগের জায়গায় ফিরে আসবে
  };

  // ---------------------------------------------------------
  // 💻 কোড ১: হ্যাকার স্টাইল GPS ট্র্যাকার
  // ---------------------------------------------------------

  // ---------------------------------------------------------
  // 💻 কোড ২: হ্যান্ড ট্র্যাকিং সিস্টেম
  // ---------------------------------------------------------

  const handleAction = (project: any) => {
    const kind = project.actionType || (project.videoUrl ? 'video' : project.code ? 'code' : 'link');
    if (kind === 'code' && project.code) {
      setActiveCode(project.code);
      setActiveFilename(project.codeFilename || 'snippet.txt');
      setShowCodeModal(true);
    } else if (kind === 'video' && project.videoUrl) {
      setActiveVideo(ytEmbed(project.videoUrl, { autoplay: true }));
      setShowVideoModal(true);
    } else if (project.url && project.url !== '#') {
      window.open(project.url, '_blank', 'noopener');
    } else {
      alert('Demo link coming soon!');
    }
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(activeCode);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  useEffect(() => {
      if (showCodeModal && codeRef.current) {
          codeRef.current.scrollTop = 0;
      }
  }, [showCodeModal]);

  return (
    <section id="projects" className="relative py-12 transition-colors duration-300 md:py-24 bg-slate-50 dark:bg-black">
      <div className="container px-4 mx-auto md:px-8">
        
        <div className="mb-10 text-center md:mb-16">
          <h2 className="mb-4 text-2xl font-extrabold md:text-4xl text-slate-900 dark:text-white">{titles.title}</h2>
          <p className="max-w-2xl mx-auto text-base md:text-lg text-slate-600 dark:text-slate-400">
             {titles.subtitle}
          </p>
        </div>

        {/* 🌟 FLAGSHIP PROJECT (project with "featured" enabled in the admin panel) 🌟 */}
        {flagship && (
        <div className="mb-12 md:mb-16" style={{ perspective: '2000px' }}>
          <div 
            ref={flagshipRef}
            onMouseMove={handleMouseMove}
            onMouseEnter={() => setIsFocused(true)}
            onMouseLeave={handleMouseLeave}
            onClick={() => { if (flagship.url && flagship.url !== '#') window.open(flagship.url, '_blank'); }}
            className="relative flex flex-col md:flex-row items-center overflow-hidden cursor-pointer rounded-3xl bg-[#0A0A0F] border border-white/10 shadow-[0_20px_60px_-15px_rgba(99,102,241,0.2)] group hover:shadow-[0_20px_80px_-15px_rgba(99,102,241,0.4)] hover:border-indigo-500/30"
            style={{
              transform: `rotateX(${tilt.x}deg) rotateY(${tilt.y}deg)`,
              transition: isFocused 
                ? 'transform 0.1s ease-out, box-shadow 0.5s ease, border-color 0.5s ease' 
                : 'transform 0.5s ease-out, box-shadow 0.5s ease, border-color 0.5s ease',
            }}
          >
            {/* ✨ Spotlight Mouse Glow Effect */}
            <div
              className="absolute inset-0 z-0 transition-opacity duration-500 pointer-events-none"
              style={{
                opacity: isFocused ? 1 : 0,
                background: `radial-gradient(800px circle at ${position.x}px ${position.y}px, rgba(99,102,241,0.12), transparent 40%)`,
              }}
            />

            {/* Background ambient glow */}
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_rgba(99,102,241,0.15)_0%,_transparent_50%)] pointer-events-none" />
            <div className="absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]" />

            {/* Text Content (Left Side) */}
            <div className="relative z-10 flex flex-col justify-center w-full p-8 md:w-1/2 md:p-12">
              <div className="inline-flex items-center gap-1.5 px-3 py-1.5 mb-6 text-xs font-bold tracking-wide text-indigo-300 uppercase border w-max rounded-full bg-indigo-500/10 border-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.3)]">
                <span className="relative flex w-2 h-2 mr-1">
                  <span className="absolute inline-flex w-full h-full bg-indigo-400 rounded-full opacity-75 animate-ping"></span>
                  <span className="relative inline-flex w-2 h-2 bg-indigo-500 rounded-full"></span>
                </span>
                {flagship?.meta || 'Flagship Product'}
              </div>
              
              <h3 className="mb-4 text-4xl font-extrabold tracking-tight text-white md:text-5xl font-display">
                {(flagship?.title || '').split(' ').slice(0, -2).join(' ')} <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">{(flagship?.title || '').split(' ').slice(-2).join(' ')}</span>
              </h3>
              
              <p className="mb-8 text-base leading-relaxed text-white/50 md:text-lg">
                {flagship?.description}
              </p>
              
              <div className="flex items-center gap-4">
                <button onClick={() => flagship && handleAction({ ...flagship, videoUrl: '' })} className="flex items-center gap-2.5 px-7 py-3 text-sm font-semibold text-black transition-transform bg-white rounded-full shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:scale-105 hover:bg-white/90">
                  <svg viewBox="0 0 384 512" fill="currentColor" className="w-[14px] h-[14px] pb-[1px] text-black">
                    <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.3 48.6-.8 90.5-90.8 103.1-125.5-44.3-18.9-62.4-59.5-62.2-85.1zM210.1 87c21.8-26.8 31.2-54.6 29.2-86.2-24.3 3.4-53.8 18.6-72.7 44.4-15.6 21-29.2 49-26 84.7 27.6 2.3 50.8-12.7 69.5-42.9z" />
                  </svg>
                  {flagship?.action || 'Explore'}
                </button>
              </div>
            </div>

            {/* Visual Presentation (Right Side - MacBook Pro Style Notch & Video) */}
            <div className="relative flex items-center justify-center w-full p-6 md:w-1/2 min-h-[300px]">
               {/* MacBook Frame Container */}
               <div className="relative z-10 w-full max-w-[450px] aspect-[16/10] bg-black rounded-t-[24px] overflow-hidden border-[6px] border-[#1a1a1a] border-b-0 shadow-2xl group-hover:shadow-indigo-500/20 transition-shadow">
                  
                  {/* The Real Notch (Overlay on top of video) */}
                  <div className="absolute top-0 left-1/2 -translate-x-1/2 h-[34px] w-[160px] bg-black rounded-b-[20px] z-20 flex items-center justify-center">
                      {/* Camera/sensor indicators */}
                      <div className="flex gap-3">
                          <div className="w-1.5 h-1.5 rounded-full bg-[#1a1a1a]"></div>
                          <div className="w-2 h-2 rounded-full bg-[#0d0d0d] border-[0.5px] border-[#333]"></div>
                      </div>
                  </div>

                  {/* The Screen Content (YouTube Video Autoplay) */}
                  <div className="relative w-full h-full overflow-hidden bg-black rounded-t-[18px]">
                     <iframe
                        src={ytEmbed(flagship?.videoUrl || "", { autoplay: true, mute: true, loop: true, controls: false })}
                        className="w-full h-full scale-[1.35] pointer-events-none"
                        title={flagship?.title || "Demo"}
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                     ></iframe>
                     <div className="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/20 via-transparent to-transparent" />
                  </div>
               </div>
            </div>

          </div>
        </div>
        )}
        {/* 🌟 END OF FLAGSHIP PROJECT 🌟 */}

        <div className="grid grid-cols-1 gap-6 md:gap-8 md:grid-cols-2 lg:grid-cols-4">
          {gridProjects.map((project) => (
            <Tilt3D key={project.id} className="h-full">
             <div className="relative flex flex-col h-full overflow-hidden transition-all duration-300 bg-white shadow-sm dark:bg-zinc-950 rounded-2xl group hover:shadow-xl dark:border-zinc-800">
                <div className="absolute inset-0 z-10 pointer-events-none bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]" />

                <div className="relative h-40 overflow-hidden md:h-48 bg-slate-200 dark:bg-zinc-900">
                  <img 
                    src={project.image} 
                    alt={project.title} 
                    className="object-cover w-full h-full transition-transform duration-700 transform group-hover:scale-110"
                  />
                  <div className="absolute inset-0 transition-colors bg-black/10 group-hover:bg-transparent" />
                </div>

                <div className="relative z-20 flex flex-col flex-grow p-5 md:p-6">
                  <h3 className="mb-2 text-lg font-bold transition-colors text-slate-900 dark:text-white group-hover:text-primary line-clamp-1">
                    {project.title}
                  </h3>
                  <p className="mb-6 text-sm font-medium text-slate-500 dark:text-slate-400 line-clamp-2">
                    {project.meta}
                  </p>
                  
                  <div className="pt-4 mt-auto border-t border-slate-100 dark:border-zinc-800">
                    <button 
                      onClick={() => handleAction(project)}
                      className="flex items-center justify-between w-full text-sm font-bold transition-colors text-primary hover:text-blue-700 dark:hover:text-blue-400"
                    >
                      {project.action}
                      <span className="bg-blue-50 dark:bg-zinc-800 p-1.5 rounded-full group-hover:bg-primary group-hover:text-white transition-all">
                          {project.videoUrl ? <Play size={16} fill="currentColor"/> :
                           project.code ? <Terminal size={16} /> :
                           project.url && project.url !== '#' ? <ExternalLink size={16} /> :
                           <ChevronRight size={16} />}
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </Tilt3D>
          ))}
        </div>
      </div>

      {/* 🟢 HACKER CODE MODAL */}
      {showCodeModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 duration-300 bg-black/90 backdrop-blur-sm animate-in fade-in">
          <div className="bg-[#0a0a0a] rounded-lg shadow-2xl w-full max-w-3xl border border-green-500/30 overflow-hidden font-mono relative">
            <div className="flex items-center justify-between px-4 py-3 bg-[#111111] border-b border-green-500/30 relative z-10">
              <div className="flex items-center gap-3">
                <Terminal size={18} className="text-green-500 animate-pulse" />
                <span className="text-sm font-bold tracking-wider text-green-400">ROOT@CYBERDECK:~# cat {activeFilename}</span>
              </div>
              <div className="flex items-center gap-2">
                <button onClick={copyToClipboard} className="p-1.5 hover:bg-green-500/20 rounded text-green-500/70">
                  {copied ? <Check size={18} /> : <Copy size={18} />}
                </button>
                <button onClick={() => setShowCodeModal(false)} className="p-1.5 hover:bg-red-900/30 text-green-500/70 rounded">
                  <X size={20} />
                </button>
              </div>
            </div>

            <div ref={codeRef} className="relative z-10 p-6 overflow-auto bg-[#0a0a0a] max-h-[70vh] custom-scrollbar">
              <pre className="text-sm leading-relaxed text-green-400 whitespace-pre">
                <code>{activeCode}</code>
              </pre>
            </div>

            <div className="px-4 py-2 bg-[#111111] border-t border-green-500/30 text-green-600/70 text-xs font-medium flex justify-between relative z-10">
              <span>STATUS: ACTIVE RUNTIME</span>
              <span>ENCRYPTION: ON</span>
            </div>
          </div>
        </div>
      )}

      {/* 🔴 VIDEO POPUP MODAL */}
      {showVideoModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 duration-300 bg-black/90 backdrop-blur-sm animate-in fade-in">
          <div className="relative w-full max-w-4xl overflow-hidden border shadow-2xl bg-slate-900 rounded-2xl border-slate-700">
            <button 
              onClick={() => setShowVideoModal(false)}
              className="absolute z-10 p-2 text-white transition-all rounded-full top-4 right-4 bg-black/50 hover:bg-red-500 backdrop-blur-md"
            >
              <X size={24} />
            </button>
            <div className="relative bg-black aspect-video">
              <iframe 
                className="w-full h-full"
                src={activeVideo}
                title="Project Demo"
                frameBorder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowFullScreen
              ></iframe>
            </div>
          </div>
        </div>
      )}

    </section>
  );
};

export default Projects;