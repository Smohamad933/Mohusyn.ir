import{c as p,a as g,j as e}from"./index-Chuop485.js";import{b as x}from"./react-vendor-D7WUTLsu.js";import{S as u}from"./star-CpgKinkv.js";import{U as b}from"./users-D0NiZYJ7.js";import{S as h}from"./shield-deDT9TPU.js";const v=p("Award",[["circle",{cx:"12",cy:"8",r:"6",key:"1vp47v"}],["path",{d:"M15.477 12.89 17 22l-5-3-5 3 1.523-9.11",key:"em7aur"}]]),y=p("Quote",[["path",{d:"M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z",key:"4rm80e"}],["path",{d:"M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z",key:"10za9r"}]]),w=p("TrendingUp",[["polyline",{points:"22 7 13.5 15.5 8.5 10.5 2 17",key:"126l90"}],["polyline",{points:"16 7 22 7 22 13",key:"kwv8wd"}]]),d=[["from-yellow-400 to-orange-500","#facc15"],["from-purple-500 to-pink-500","#a855f7"],["from-orange-500 to-red-500","#f97316"],["from-green-500 to-teal-500","#22c55e"],["from-blue-500 to-cyan-500","#3b82f6"],["from-pink-500 to-rose-500","#ec4899"],["from-indigo-500 to-violet-500","#6366f1"],["from-emerald-500 to-lime-500","#10b981"]],j=t=>{if(!t)return"";const s=new Date(t);if(isNaN(s.getTime()))return t;const a=Math.floor((Date.now()-s.getTime())/864e5);return a<=0?"Today":a===1?"Yesterday":a<7?`${a} days ago`:a<30?`${Math.floor(a/7)} week${a>=14?"s":""} ago`:a<365?`${Math.floor(a/30)} months ago`:`${Math.floor(a/365)} years ago`},k=(t,s)=>({id:t.id,name:t.name,rating:t.rating||5,message:t.text||t.message,ratingText:t.role||t.ratingText||"Reviewer",color:d[s%d.length][0],accent:d[s%d.length][1],photoURL:t.avatar||t.photoURL||"",verified:!!t.verified,date:j(t.createdAt)}),N=(t,s=1500)=>{const[a,r]=x.useState(0);return x.useEffect(()=>{let l=0;const i=t/(s/16),o=setInterval(()=>{l+=i,l>=t?(r(t),clearInterval(o)):r(Math.floor(l))},16);return()=>clearInterval(o)},[t,s]),a},z=({rating:t})=>e.jsx("div",{className:"flex gap-0.5",children:[...Array(5)].map((s,a)=>e.jsx(u,{size:12,className:`${a<t?"text-amber-400":"text-slate-600"} sm:w-[14px] sm:h-[14px]`,fill:a<t?"currentColor":"none",style:a<t?{filter:"drop-shadow(0 0 4px rgba(251,191,36,0.6))"}:{}},a))}),f=({item:t})=>e.jsx("div",{className:"feedback-card w-[260px] sm:w-[300px] md:w-[360px] flex-shrink-0 whitespace-normal relative group",style:{"--accent":t.accent||"#facc15"},children:e.jsxs("div",{className:"relative h-full p-4 overflow-hidden transition-all duration-300 border sm:p-5 rounded-xl sm:rounded-2xl bg-white/80 dark:bg-zinc-900/80 border-slate-200/60 dark:border-zinc-800/60 backdrop-blur-md group-hover:border-opacity-100 group-hover:shadow-xl",children:[e.jsx("div",{className:"absolute inset-0 transition-opacity duration-500 opacity-0 pointer-events-none group-hover:opacity-100 rounded-xl sm:rounded-2xl",style:{background:`radial-gradient(circle at 50% 0%, ${t.accent}18 0%, transparent 70%)`}}),e.jsx("div",{className:"absolute top-0 h-px transition-opacity duration-300 opacity-0 left-4 right-4 sm:left-6 sm:right-6 group-hover:opacity-100",style:{background:`linear-gradient(to right, transparent, ${t.accent}, transparent)`}}),e.jsx(y,{className:"absolute transition-all duration-300 top-3 right-3 sm:top-4 sm:right-4 opacity-10 group-hover:opacity-20",style:{color:t.accent},size:28}),e.jsxs("div",{className:"flex items-center justify-between mb-2.5 sm:mb-3",children:[e.jsx(z,{rating:t.rating}),e.jsxs("div",{className:"flex items-center gap-1.5 sm:gap-2",children:[t.verified&&e.jsxs("span",{className:"flex items-center gap-0.5 sm:gap-1 text-[9px] sm:text-[10px] font-semibold px-1.5 sm:px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20",children:[e.jsx(h,{size:8,className:"sm:w-[9px] sm:h-[9px]"}),e.jsx("span",{className:"hidden xs:inline",children:"Verified"}),e.jsx("span",{className:"xs:hidden",children:"✓"})]}),e.jsx("span",{className:"text-[9px] sm:text-[10px] text-slate-400 dark:text-slate-500 hidden sm:inline",children:t.date||""})]})]}),e.jsxs("p",{className:"mb-4 text-xs font-medium leading-relaxed sm:mb-5 sm:text-sm text-slate-600 dark:text-slate-300 font-bangla-premium line-clamp-3",children:['"',t.message||"No comment provided.",'"']}),e.jsxs("div",{className:"flex items-center gap-2.5 sm:gap-3 pt-2.5 sm:pt-3 border-t border-slate-100 dark:border-zinc-800",children:[e.jsxs("div",{className:"relative shrink-0",children:[t.photoURL?e.jsx("img",{src:t.photoURL,alt:t.name||"User",onError:s=>{var a;s.target.style.display="none",(a=s.target.nextElementSibling)==null||a.classList.remove("hidden")},className:"object-cover border-2 rounded-full shadow-md w-9 h-9 sm:w-10 sm:h-10",style:{borderColor:`${t.accent}40`}}):null,e.jsx("div",{className:`${t.photoURL?"hidden":"flex"} items-center justify-center w-9 h-9 sm:w-10 sm:h-10 text-sm sm:text-base font-bold text-white rounded-full shadow-md bg-gradient-to-tr ${t.color||"from-yellow-400 to-orange-500"}`,children:t.name?t.name.charAt(0).toUpperCase():"U"}),e.jsx("span",{className:"absolute bottom-0 right-0 w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full border-2 border-white dark:border-zinc-900 bg-emerald-400"})]}),e.jsxs("div",{className:"min-w-0",children:[e.jsx("h4",{className:"text-xs font-bold truncate sm:text-sm text-slate-900 dark:text-white",children:t.name||"Anonymous"}),e.jsx("span",{className:"text-[9px] sm:text-[10px] font-semibold uppercase tracking-wider",style:{color:t.accent},children:t.ratingText||"Reviewer"})]})]})]})}),m=({icon:t,value:s,label:a,color:r,suffix:l=""})=>{const i=N(s);return e.jsxs("div",{className:"flex items-center gap-2 px-3 py-2 sm:gap-3 sm:px-6 sm:py-3",children:[e.jsx("div",{className:"p-1.5 sm:p-2 rounded-lg sm:rounded-xl",style:{background:`${r}15`},children:e.jsx(t,{size:16,className:"sm:w-[18px] sm:h-[18px]",style:{color:r}})}),e.jsxs("div",{children:[e.jsxs("p",{className:"text-base font-black leading-none sm:text-lg text-slate-900 dark:text-white",children:[i,l]}),e.jsx("p",{className:"text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-wider mt-0.5",children:a})]})]})},U=()=>{const{content:t}=g(),s=t.settings.feedbackStats,a=t.settings.sectionTitles.testimonials,r=x.useMemo(()=>t.testimonials.filter(n=>n.approved!==!1).map(k),[t.testimonials]),l=Math.ceil(r.length/2),i=r.slice(0,l),o=r.slice(l).length>0?r.slice(l):i;return e.jsxs("section",{className:"relative py-12 sm:py-16 md:py-24 overflow-hidden transition-colors duration-300 bg-slate-50 dark:bg-[#080808]",children:[e.jsx("style",{children:`
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap');

        .font-bangla-premium { font-family: 'Hind Siliguri', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        @keyframes marquee-left {
          0% { transform: translateX(0); }
          100% { transform: translateX(-50%); }
        }
        @keyframes marquee-right {
          0% { transform: translateX(-50%); }
          100% { transform: translateX(0); }
        }

        .animate-marquee-left {
          animation: marquee-left 35s linear infinite;
        }
        .animate-marquee-right {
          animation: marquee-right 42s linear infinite;
        }
        .animate-marquee-left:hover,
        .animate-marquee-right:hover {
          animation-play-state: paused;
        }

        /* Mobile-friendly marquee speed */
        @media (max-width: 640px) {
          .animate-marquee-left {
            animation: marquee-left 40s linear infinite;
          }
          .animate-marquee-right {
            animation: marquee-right 48s linear infinite;
          }
        }

        .mask-gradient-x {
          mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
          -webkit-mask-image: linear-gradient(to right, transparent 0%, black 8%, black 92%, transparent 100%);
        }

        .feedback-card {
          transition: transform 0.3s ease;
        }
        .feedback-card:hover {
          transform: translateY(-4px) scale(1.01);
        }

        /* Reduce hover effect on touch devices */
        @media (hover: none) {
          .feedback-card:hover {
            transform: translateY(-2px) scale(1.005);
          }
        }

        @keyframes float-slow {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-18px) rotate(3deg); }
        }
        @keyframes float-medium {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-12px) rotate(-2deg); }
        }
        .float-slow { animation: float-slow 8s ease-in-out infinite; }
        .float-medium { animation: float-medium 6s ease-in-out infinite; }

        @keyframes pulse-glow {
          0%, 100% { opacity: 0.3; }
          50% { opacity: 0.6; }
        }
        .pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }

        .stat-divider {
          width: 1px;
          height: 28px;
          background: linear-gradient(to bottom, transparent, rgba(148,163,184,0.3), transparent);
        }

        @media (min-width: 640px) {
          .stat-divider {
            height: 36px;
          }
        }

        .badge-glow {
          box-shadow: 0 0 12px rgba(234,179,8,0.25);
        }

        /* Custom breakpoint for extra small screens */
        @media (min-width: 380px) {
          .xs:inline {
            display: inline;
          }
          .xs:hidden {
            display: none;
          }
        }
      `}),e.jsxs("div",{className:"absolute inset-0 overflow-hidden pointer-events-none",children:[e.jsx("div",{className:"absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] rounded-full pulse-glow",style:{background:"radial-gradient(circle, rgba(234,179,8,0.06) 0%, transparent 70%)"}}),e.jsx("div",{className:"absolute -top-20 -left-20 w-60 sm:w-80 h-60 sm:h-80 rounded-full blur-[60px] sm:blur-[80px] opacity-20",style:{background:"radial-gradient(circle, rgba(139,92,246,0.4), transparent)"}}),e.jsx("div",{className:"absolute -bottom-20 -right-20 w-60 sm:w-80 h-60 sm:h-80 rounded-full blur-[60px] sm:blur-[80px] opacity-20",style:{background:"radial-gradient(circle, rgba(6,182,212,0.4), transparent)"}}),e.jsx("div",{className:"hidden sm:block float-slow absolute top-20 left-[12%] w-3 h-3 rounded-full bg-amber-400/30 blur-[2px]"}),e.jsx("div",{className:"hidden sm:block float-medium absolute top-40 right-[15%] w-2 h-2 rounded-full bg-purple-400/40",style:{animationDelay:"1s"}}),e.jsx("div",{className:"hidden sm:block float-slow absolute bottom-32 left-[20%] w-2 h-2 rounded-full bg-cyan-400/40",style:{animationDelay:"2s"}}),e.jsx("div",{className:"hidden sm:block float-medium absolute bottom-20 right-[25%] w-3 h-3 rounded-full bg-rose-400/30 blur-[1px]",style:{animationDelay:"0.5s"}}),e.jsx("div",{className:"absolute inset-0 opacity-[0.015] dark:opacity-[0.03]",style:{backgroundImage:"radial-gradient(circle, #94a3b8 1px, transparent 1px)",backgroundSize:"40px 40px"}})]}),e.jsxs("div",{className:"container relative z-10 px-4 mx-auto mb-6 text-center sm:mb-8 md:mb-10",children:[e.jsxs("div",{className:"inline-flex items-center gap-1.5 sm:gap-2 mb-3 sm:mb-5 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full border border-amber-500/25 bg-amber-500/8 backdrop-blur-sm badge-glow",children:[e.jsx(v,{size:11,className:"sm:w-[13px] sm:h-[13px] text-amber-400"}),e.jsx("span",{className:"text-[10px] sm:text-xs font-semibold tracking-widest uppercase text-amber-500",children:"Client Testimonials"})]}),e.jsxs("h2",{className:"mb-3 text-3xl font-extrabold leading-tight transition-colors duration-300 sm:mb-4 sm:text-4xl md:text-6xl font-display text-slate-900 dark:text-white",children:[a.title.split(" ").slice(0,-2).join(" ")||a.title.split(" ")[0]," ",e.jsxs("span",{className:"relative inline-block text-transparent bg-clip-text",style:{backgroundImage:"linear-gradient(135deg, #f59e0b 0%, #ef4444 50%, #ec4899 100%)"},children:[a.title.split(" ").length>2?a.title.split(" ").slice(-2).join(" "):a.title.split(" ").slice(1).join(" "),e.jsxs("svg",{className:"absolute left-0 w-full -bottom-1 sm:-bottom-2",height:"6",viewBox:"0 0 200 6",fill:"none",children:[e.jsx("path",{d:"M0 3 Q50 0 100 3 Q150 6 200 3",stroke:"url(#ul-grad)",strokeWidth:"2.5",strokeLinecap:"round",fill:"none"}),e.jsx("defs",{children:e.jsxs("linearGradient",{id:"ul-grad",x1:"0",y1:"0",x2:"200",y2:"0",children:[e.jsx("stop",{offset:"0%",stopColor:"#f59e0b"}),e.jsx("stop",{offset:"100%",stopColor:"#ec4899"})]})})]})]})]}),e.jsx("p",{className:"max-w-md mx-auto text-sm leading-relaxed sm:text-base text-slate-500 dark:text-slate-400",children:a.subtitle})]}),e.jsx("div",{className:"container relative z-10 px-4 mx-auto mb-6 sm:mb-8 md:mb-10",children:e.jsxs("div",{className:"grid max-w-2xl grid-cols-2 gap-0 mx-auto overflow-hidden border shadow-sm sm:flex sm:flex-wrap sm:items-center sm:justify-center rounded-xl sm:rounded-2xl border-slate-200/60 dark:border-zinc-800/60 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-sm",children:[e.jsx(m,{icon:u,value:parseInt(s.satisfaction)||0,suffix:s.satisfaction.replace(/[0-9]/g,""),label:"Satisfaction",color:"#f59e0b"}),e.jsx("div",{className:"hidden sm:block stat-divider"}),e.jsx(m,{icon:b,value:parseInt(s.reviews)||0,suffix:s.reviews.replace(/[0-9]/g,""),label:"Reviews",color:"#6366f1"}),e.jsx("div",{className:"hidden sm:block stat-divider"}),e.jsx(m,{icon:w,value:parseInt(s.rating)||0,suffix:s.rating.replace(/[0-9]/g,""),label:"Avg Rating",color:"#22c55e"}),e.jsx("div",{className:"hidden sm:block stat-divider"}),e.jsx(m,{icon:h,value:parseInt(s.verified)||0,suffix:s.verified.replace(/[0-9]/g,""),label:"Verified",color:"#ec4899"})]})}),e.jsx("div",{className:"relative flex w-full mb-3 overflow-hidden sm:mb-4 mask-gradient-x",children:e.jsx("div",{className:"flex gap-3 py-2 sm:gap-5 sm:py-3 animate-marquee-left",children:[...i,...i,...i].map((n,c)=>e.jsx(f,{item:n},`row1-${n.id}-${c}`))})}),e.jsx("div",{className:"relative flex w-full overflow-hidden mask-gradient-x",children:e.jsx("div",{className:"flex gap-3 py-2 sm:gap-5 sm:py-3 animate-marquee-right",children:[...o,...o,...o].map((n,c)=>e.jsx(f,{item:n},`row2-${n.id}-${c}`))})}),e.jsx("div",{className:"container relative z-10 px-4 mx-auto mt-8 text-center sm:mt-12",children:e.jsx("p",{className:"text-xs sm:text-sm text-slate-500 dark:text-slate-500",children:e.jsx("span",{className:"font-semibold text-slate-700 dark:text-slate-300",children:s.footer})})})]})};export{U as default};
