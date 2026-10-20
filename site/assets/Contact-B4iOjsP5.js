var Y=Object.defineProperty,M=Object.defineProperties;var F=Object.getOwnPropertyDescriptors;var h=Object.getOwnPropertySymbols;var I=Object.prototype.hasOwnProperty,P=Object.prototype.propertyIsEnumerable;var g=(t,a,r)=>a in t?Y(t,a,{enumerable:!0,configurable:!0,writable:!0,value:r}):t[a]=r,f=(t,a)=>{for(var r in a||(a={}))I.call(a,r)&&g(t,r,a[r]);if(h)for(var r of h(a))P.call(a,r)&&g(t,r,a[r]);return t},k=(t,a)=>M(t,F(a));import{u as T,e as E,P as $,G as _,L as q,f as j,I as D,F as G,Y as L,M as R,j as e,S as v,d as B}from"./index-DuEQaH5e.js";import{b as n}from"./react-vendor-D7WUTLsu.js";import{M as X}from"./map-pin-16FNpC8L.js";import{Z as y}from"./zap-C18ARQ2u.js";import{C as Z}from"./check-circle-2-DTcHrO4W.js";const A=`
  @import url('https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;900&display=swap');

  #contact { font-family: 'Cabinet Grotesk', sans-serif; }

  /* Shimmer heading */
  @keyframes shimmer-pan {
    0%   { background-position: 0% center; }
    100% { background-position: 200% center; }
  }
  .contact-shimmer-text {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer-pan 5s linear infinite;
  }

  /* Floating badges */
  @keyframes float-a {
    0%, 100% { transform: translateY(0) rotate(-1deg); }
    50%       { transform: translateY(-12px) rotate(2deg); }
  }
  @keyframes float-b {
    0%, 100% { transform: translateY(0) rotate(1deg); }
    50%       { transform: translateY(-16px) rotate(-2deg); }
  }
  .contact-badge-a { animation: float-a 4s ease-in-out infinite; }
  .contact-badge-b { animation: float-b 5s ease-in-out infinite; animation-delay: -2.5s; }

  /* Rotating ring */
  @keyframes spin-slow { to { transform: rotate(360deg); } }
  .contact-ring { animation: spin-slow 12s linear infinite; }

  /* Orb pulse */
  @keyframes orb-pulse {
    0%, 100% { opacity: 0.12; transform: scale(1); }
    50%       { opacity: 0.22; transform: scale(1.08); }
  }
  .contact-orb-1 { animation: orb-pulse 7s ease-in-out infinite; }
  .contact-orb-2 { animation: orb-pulse 9s ease-in-out infinite; animation-delay: -4s; }

  /* Submit button shine */
  .contact-submit-btn {
    position: relative;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .contact-submit-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
  }
  .contact-submit-btn:hover::after  { transform: translateX(100%); }
  .contact-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(59,130,246,0.4); }

  /* Input focus effect */
  .contact-input {
    transition: all 0.3s ease;
  }
  .contact-input:focus {
    border-color: rgb(59 130 246 / 0.5);
    box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
  }

  /* Card hover */
  .contact-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  }
  .contact-card:hover { transform: translateY(-3px); }

  /* Social icon hover */
  .contact-social {
    transition: all 0.3s ease;
  }
  .contact-social:hover {
    transform: translateY(-4px) scale(1.1);
  }

  /* Status dot */
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
  .contact-dot { animation: blink 1.8s ease-in-out infinite; }

  /* Success message slide */
  @keyframes slide-in {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .contact-success {
    animation: slide-in 0.4s ease-out;
  }
`,U=()=>{const t=T(),a=t.sectionTitles.contact,[r,d]=n.useState(!1),[u,b]=n.useState(""),[o,x]=n.useState({name:"",email:"",subject:"",message:""}),[w,p]=n.useState(!1),l=n.useRef(null),N=async s=>{s.preventDefault(),l.current&&clearTimeout(l.current),b(""),d(!0);try{await B("contact",o)}catch(i){d(!1),b(i.message||"Could not send your message. Please try again.");return}d(!1),p(!0),l.current=setTimeout(()=>{p(!1),x({name:"",email:"",subject:"",message:""}),l.current=null},3e3)};n.useEffect(()=>()=>{l.current&&(clearTimeout(l.current),l.current=null)},[]);const c=s=>{x(k(f({},o),{[s.target.name]:s.target.value}))},z=[t.socials.email?{icon:E,title:"Email",value:t.socials.email,link:`mailto:${t.socials.email}`,color:"blue"}:null,t.socials.phone?{icon:$,title:"Phone",value:t.socials.phone,link:`tel:${t.socials.phone.replace(/[^0-9+]/g,"")}`,color:"purple"}:null,t.socials.location?{icon:X,title:"Location",value:t.socials.location,link:"#",color:"pink"}:null].filter(Boolean),S=[t.socials.github?{icon:_,link:t.socials.github,color:"hover:text-slate-900 dark:hover:text-white"}:null,t.socials.linkedin?{icon:q,link:t.socials.linkedin,color:"hover:text-blue-600"}:null,t.socials.telegram?{icon:j,link:t.socials.telegram.startsWith("http")?t.socials.telegram:`https://t.me/${t.socials.telegram.replace("@","")}`,color:"hover:text-sky-500"}:null,t.socials.instagram?{icon:D,link:t.socials.instagram,color:"hover:text-pink-500"}:null,t.socials.facebook?{icon:G,link:t.socials.facebook,color:"hover:text-blue-500"}:null,t.socials.youtube?{icon:L,link:t.socials.youtube,color:"hover:text-red-500"}:null,t.socials.whatsapp?{icon:R,link:`https://wa.me/${t.socials.whatsapp.replace(/[^0-9]/g,"")}`,color:"hover:text-green-500"}:null].filter(Boolean);return e.jsxs(e.Fragment,{children:[e.jsx("style",{dangerouslySetInnerHTML:{__html:A}}),e.jsxs("section",{id:"contact",className:"relative py-32 overflow-hidden transition-colors duration-500 bg-gradient-to-br from-slate-50 via-white to-blue-50/30 dark:from-black dark:via-zinc-950 dark:to-black",children:[e.jsxs("div",{className:"absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none",children:[e.jsx("div",{className:"contact-orb-1 absolute top-10 left-[-10%] w-[550px] h-[550px] bg-blue-500/10 rounded-full blur-[130px]"}),e.jsx("div",{className:"contact-orb-2 absolute bottom-10 right-[-10%] w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[120px]"}),e.jsx("div",{className:"absolute inset-0 opacity-[0.018] dark:opacity-[0.035]",style:{backgroundImage:"linear-gradient(rgba(99,102,241,1) 1px, transparent 1px), linear-gradient(90deg, rgba(99,102,241,1) 1px, transparent 1px)",backgroundSize:"70px 70px"}})]}),e.jsxs("div",{className:"container relative z-10 px-4 mx-auto md:px-8",children:[e.jsx("div",{className:"flex justify-center mb-10",children:e.jsxs("div",{className:"inline-flex items-center gap-2 px-5 py-2 text-[10px] font-bold tracking-[0.3em] text-blue-600 dark:text-blue-400 uppercase border border-blue-200/70 dark:border-blue-800/50 rounded-full bg-blue-50/80 dark:bg-blue-900/15 backdrop-blur-sm",children:[e.jsx(v,{size:12,className:"animate-pulse"}),"Get In Touch",e.jsx(v,{size:12,className:"animate-pulse"})]})}),e.jsxs("div",{className:"flex flex-col lg:flex-row gap-8 p-8 md:p-12 lg:p-16 bg-white/70 dark:bg-zinc-900/40 backdrop-blur-2xl border border-slate-200/80 dark:border-white/8 rounded-[2.5rem] shadow-2xl shadow-slate-200/60 dark:shadow-[0_0_60px_rgba(0,0,0,0.6)]",children:[e.jsxs("div",{className:"relative flex flex-col w-full space-y-8 lg:w-2/5",children:[e.jsx("div",{className:"contact-ring absolute top-20 left-1/2 -translate-x-1/2 w-[280px] h-[280px] rounded-full border border-dashed border-purple-400/20 dark:border-purple-500/15 pointer-events-none"}),e.jsxs("div",{className:"relative z-10 text-center lg:text-left",children:[e.jsxs("h2",{className:"text-4xl font-black leading-tight md:text-5xl text-slate-900 dark:text-white",children:[a.title.split(" ").slice(0,-1).join(" ")," ",e.jsx("span",{className:"contact-shimmer-text",children:a.title.split(" ").slice(-1)[0]})]}),e.jsxs("p",{className:"flex items-center justify-center gap-2 mt-3 text-base font-medium lg:justify-start text-slate-500 dark:text-slate-400",children:[e.jsx(y,{size:14,className:"text-yellow-400 shrink-0"}),a.subtitle]})]}),e.jsx("div",{className:"relative z-10 space-y-4",children:z.map((s,i)=>{const m=s.icon,C={blue:"bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-400/50 dark:border-blue-500/40",purple:"bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 border-purple-400/50 dark:border-purple-500/40",pink:"bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 border-pink-400/50 dark:border-pink-500/40"};return e.jsxs("a",{href:s.link,className:"flex items-center gap-4 p-5 bg-white border contact-card dark:bg-zinc-900/50 border-slate-200 dark:border-zinc-800 rounded-2xl hover:shadow-lg group",children:[e.jsx("div",{className:`p-3 rounded-xl transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 ${C[s.color]}`,children:e.jsx(m,{size:22})}),e.jsxs("div",{className:"text-left",children:[e.jsx("h4",{className:"text-sm font-black text-slate-900 dark:text-white",children:s.title}),e.jsx("p",{className:"text-xs text-slate-500 dark:text-slate-500 mt-0.5",children:s.value})]})]},i)})}),e.jsxs("div",{className:"relative z-10",children:[e.jsx("p",{className:"text-[10px] font-bold tracking-[0.3em] text-slate-400 uppercase mb-3 text-center lg:text-left",children:"Connect on Social"}),e.jsx("div",{className:"flex justify-center gap-3 lg:justify-start",children:S.map((s,i)=>{const m=s.icon;return e.jsx("a",{href:s.link,target:"_blank",rel:"noopener noreferrer",className:`contact-social p-3.5 bg-white dark:bg-zinc-900/70 border border-slate-200 dark:border-zinc-800 rounded-xl text-slate-400 ${s.color}`,children:e.jsx(m,{size:20})},i)})})]}),e.jsxs("div",{className:"relative z-10 flex items-center justify-center gap-2 px-4 py-3 text-sm border lg:justify-start bg-emerald-50/80 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-800/50 rounded-2xl",children:[e.jsx("span",{className:"inline-block w-2 h-2 rounded-full contact-dot bg-emerald-400"}),e.jsx("span",{className:"font-bold text-emerald-600 dark:text-emerald-400",children:t.contact.availability})]}),e.jsxs("div",{className:"contact-badge-b absolute z-20 top-5 -right-5 hidden lg:flex items-center gap-2.5 px-4 py-3 bg-white dark:bg-zinc-900 border border-slate-100 dark:border-zinc-700/80 rounded-2xl shadow-xl shadow-purple-100/60 dark:shadow-black/50",children:[e.jsx("div",{className:"p-2 bg-purple-50 dark:bg-purple-900/30 rounded-xl",children:e.jsx(y,{className:"w-5 h-5 text-purple-500"})}),e.jsxs("div",{children:[e.jsx("p",{className:"text-[10px] text-slate-400 font-medium",children:"Projects Done"}),e.jsx("p",{className:"text-xs font-black text-slate-800 dark:text-white",children:t.contact.projectsDone})]})]})]}),e.jsx("div",{className:"w-full lg:w-3/5",children:e.jsxs("form",{onSubmit:N,className:"space-y-5",children:[e.jsxs("div",{className:"grid grid-cols-1 gap-5 md:grid-cols-2",children:[e.jsxs("div",{children:[e.jsx("label",{htmlFor:"name",className:"block mb-2 text-sm font-bold text-slate-700 dark:text-slate-300",children:"Your Name"}),e.jsx("input",{type:"text",id:"name",name:"name",value:o.name,onChange:c,required:!0,className:"contact-input w-full px-4 py-3.5 bg-white dark:bg-zinc-900/80 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none",placeholder:"Your name"})]}),e.jsxs("div",{children:[e.jsx("label",{htmlFor:"email",className:"block mb-2 text-sm font-bold text-slate-700 dark:text-slate-300",children:"Email Address"}),e.jsx("input",{type:"email",id:"email",name:"email",value:o.email,onChange:c,required:!0,className:"contact-input w-full px-4 py-3.5 bg-white dark:bg-zinc-900/80 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none",placeholder:"you@example.com"})]})]}),e.jsxs("div",{children:[e.jsx("label",{htmlFor:"subject",className:"block mb-2 text-sm font-bold text-slate-700 dark:text-slate-300",children:"Subject"}),e.jsx("input",{type:"text",id:"subject",name:"subject",value:o.subject,onChange:c,required:!0,className:"contact-input w-full px-4 py-3.5 bg-white dark:bg-zinc-900/80 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none",placeholder:"Project collaboration opportunity"})]}),e.jsxs("div",{children:[e.jsx("label",{htmlFor:"message",className:"block mb-2 text-sm font-bold text-slate-700 dark:text-slate-300",children:"Your Message"}),e.jsx("textarea",{id:"message",name:"message",value:o.message,onChange:c,required:!0,rows:6,className:"contact-input w-full px-4 py-3.5 bg-white dark:bg-zinc-900/80 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none resize-none",placeholder:"Tell me about your project or idea..."})]}),w&&e.jsxs("div",{className:"flex items-center gap-3 p-4 border contact-success bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50 rounded-xl",children:[e.jsx(Z,{className:"w-5 h-5 text-emerald-500 shrink-0"}),e.jsx("p",{className:"text-sm font-semibold text-emerald-700 dark:text-emerald-400",children:"Message sent successfully! I'll get back to you soon."})]}),u&&e.jsx("p",{className:"text-sm font-semibold text-red-500",children:u}),e.jsxs("button",{type:"submit",disabled:r,className:"inline-flex items-center justify-center w-full gap-2 px-10 py-4 text-base font-bold text-white contact-submit-btn md:w-auto bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl disabled:opacity-60",children:[r?"Sending…":"Send Message"," ",e.jsx(j,{size:16})]})]})})]}),e.jsx("div",{className:"mt-10 text-center",children:e.jsx("p",{className:"text-sm text-slate-400 dark:text-slate-600",children:t.socials.email&&e.jsxs(e.Fragment,{children:["Prefer a quick chat? Email me directly at"," ",e.jsx("a",{href:`mailto:${t.socials.email}`,className:"font-bold text-blue-500 hover:underline",children:t.socials.email})]})})})]})]})]})};export{U as default};
