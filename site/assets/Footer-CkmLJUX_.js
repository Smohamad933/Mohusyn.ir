import{u as c,G as d,g as x,I as m,f as p,j as e,S as l,A as f}from"./index-CFFIX-xn.js";import{C as b}from"./code-2-BVwVIM82.js";import{Z as h}from"./zap-BzERY9w1.js";import{H as u}from"./heart-BDVpDFba.js";import"./react-vendor-D7WUTLsu.js";const g=`
  @import url('https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;900&display=swap');

  #footer { font-family: 'Cabinet Grotesk', sans-serif; }

  /* Shimmer text */
  @keyframes shimmer-pan {
    0%   { background-position: 0% center; }
    100% { background-position: 200% center; }
  }
  .footer-shimmer {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer-pan 5s linear infinite;
  }

  /* Heart beat */
  @keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    10%, 30% { transform: scale(1.1); }
    20%, 40% { transform: scale(0.95); }
  }
  .footer-heart { animation: heartbeat 1.5s ease-in-out infinite; }

  /* Social icon hover */
  .footer-social {
    transition: all 0.3s ease;
  }
  .footer-social:hover {
    transform: translateY(-4px) scale(1.1) rotate(5deg);
  }

  /* Link hover */
  .footer-link {
    position: relative;
    transition: color 0.3s ease;
  }
  .footer-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transition: width 0.3s ease;
  }
  .footer-link:hover::after { width: 100%; }

  /* Back to top button */
  .footer-top-btn {
    transition: all 0.3s ease;
  }
  .footer-top-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(59,130,246,0.3);
  }

  /* Orb pulse */
  @keyframes orb-pulse {
    0%, 100% { opacity: 0.08; transform: scale(1); }
    50%       { opacity: 0.15; transform: scale(1.05); }
  }
  .footer-orb { animation: orb-pulse 8s ease-in-out infinite; }
`,y=()=>{const i=()=>{window.scrollTo({top:0,behavior:"smooth"})},t=c(),s=t.fullName.split(" "),o={explore:[{name:"Home",href:"#home"},{name:"About",href:"#about"},{name:"Projects",href:"#projects"},{name:"Contact",href:"#contact"},{name:"Help & Support",href:"/support"},{name:"Terms & Conditions",href:"/terms"},{name:"Privacy Policy",href:"/privacy"}],social:[t.socials.github?{icon:d,href:t.socials.github,label:"GitHub"}:null,t.socials.linkedin?{icon:x,href:t.socials.linkedin,label:"LinkedIn"}:null,t.socials.instagram?{icon:m,href:t.socials.instagram,label:"Instagram"}:null,t.socials.email?{icon:p,href:`mailto:${t.socials.email}`,label:"Email"}:null].filter(Boolean)};return e.jsxs(e.Fragment,{children:[e.jsx("style",{dangerouslySetInnerHTML:{__html:g}}),e.jsxs("footer",{id:"footer",className:"relative py-16 overflow-hidden transition-colors duration-500 border-t bg-gradient-to-br from-slate-50 via-white to-blue-50/30 dark:from-black dark:via-zinc-950 dark:to-black border-slate-200/80 dark:border-white/8",children:[e.jsxs("div",{className:"absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none",children:[e.jsx("div",{className:"footer-orb absolute -top-20 left-[10%] w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[100px]"}),e.jsx("div",{className:"footer-orb absolute -bottom-20 right-[10%] w-[350px] h-[350px] bg-purple-500/10 rounded-full blur-[90px]"}),e.jsx("div",{className:"absolute inset-0 opacity-[0.015] dark:opacity-[0.03]",style:{backgroundImage:"linear-gradient(rgba(99,102,241,1) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,1) 1px, transparent 1px)",backgroundSize:"60px 60px"}})]}),e.jsxs("div",{className:"container relative z-10 px-4 mx-auto md:px-8",children:[e.jsxs("div",{className:"grid grid-cols-1 gap-12 pb-12 border-b md:grid-cols-3 border-slate-200/60 dark:border-zinc-800/60",children:[e.jsxs("div",{className:"text-center md:text-left",children:[e.jsxs("div",{className:"inline-flex items-center gap-2 mb-4",children:[e.jsx("div",{className:"p-2 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600",children:e.jsx(b,{className:"w-6 h-6 text-white"})}),e.jsxs("h3",{className:"text-2xl font-black text-slate-900 dark:text-white",children:[s.slice(0,-1).join(" ")," ",e.jsx("span",{className:"footer-shimmer",children:s.slice(-1)[0]})]})]}),e.jsx("p",{className:"text-sm leading-relaxed text-slate-600 dark:text-slate-400",children:t.footer.quote}),e.jsxs("div",{className:"flex items-center justify-center gap-2 mt-4 text-sm md:justify-start text-slate-500 dark:text-slate-500",children:[e.jsx(h,{size:14,className:"text-yellow-400"}),e.jsx("span",{className:"font-semibold",children:t.contact.availability})]})]}),e.jsxs("div",{className:"text-center",children:[e.jsxs("h4",{className:"text-[11px] font-bold tracking-[0.3em] text-slate-400 uppercase mb-4 flex items-center justify-center gap-2",children:[e.jsx(l,{size:11}),"Explore"]}),e.jsx("ul",{className:"grid grid-cols-2 gap-x-4 gap-y-3 max-w-[280px] mx-auto",children:o.explore.map((a,r)=>e.jsx("li",{children:e.jsx("a",{href:a.href,className:"inline-block text-sm font-semibold footer-link text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400",children:a.name})},r))})]}),e.jsxs("div",{className:"text-center md:text-right",children:[e.jsxs("h4",{className:"text-[11px] font-bold tracking-[0.3em] text-slate-400 uppercase mb-4 flex items-center justify-center md:justify-end gap-2",children:[e.jsx(l,{size:11}),"Connect"]}),e.jsx("div",{className:"flex justify-center gap-3 mb-4 md:justify-end",children:o.social.map((a,r)=>{const n=a.icon;return e.jsx("a",{href:a.href,target:"_blank",rel:"noopener noreferrer","aria-label":a.label,className:"p-3 bg-white border footer-social dark:bg-zinc-900/70 border-slate-200 dark:border-zinc-800 rounded-xl text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-400/50 dark:hover:border-blue-500/40",children:e.jsx(n,{size:18})},r)})}),e.jsx("p",{className:"text-xs text-slate-500 dark:text-slate-500",children:"Let's build something amazing together"})]})]}),e.jsxs("div",{className:"flex flex-col items-center justify-between gap-4 pt-8 md:flex-row",children:[e.jsxs("p",{className:"text-sm text-slate-500 dark:text-slate-500",children:["© ",new Date().getFullYear()," ",e.jsx("span",{className:"font-bold text-slate-700 dark:text-slate-400",children:t.fullName}),". ",t.footer.text.replace("❤️","").replace("with","with "),e.jsx(u,{className:"inline w-4 h-4 footer-heart text-rose-500",fill:"currentColor"})]}),e.jsxs("button",{onClick:i,className:"footer-top-btn inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg","aria-label":"Back to top",children:["Back to Top",e.jsx(f,{size:14})]})]}),e.jsxs("div",{className:"flex flex-wrap items-center justify-center gap-2 pt-8 mt-8 border-t border-slate-200/60 dark:border-zinc-800/60",children:[e.jsx("span",{className:"text-[10px] font-semibold text-slate-400 tracking-wider",children:"BUILT WITH:"}),["React","TypeScript","Tailwind CSS","Lucide Icons"].map((a,r)=>e.jsx("span",{className:"px-3 py-1 text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-zinc-900/60 border border-slate-200 dark:border-zinc-800 rounded-full",children:a},r))]})]})]})]})};export{y as default};
