import{j as s}from"./index-CFFIX-xn.js";import"./react-vendor-D7WUTLsu.js";function l(...e){return e.filter(Boolean).join(" ")}const o=Array.from({length:38},(e,t)=>({id:t,cx:18+Math.sin(t*1.7)*14,cy:15+Math.cos(t*2.3)*28,r:.12+t%5*.08,dur:7+t%7*2.4,delay:t%9*.9,dx:-1.2+t%3*.8,dy:-2.4+t%4*.5,opacity:.18+t%4*.07}));function r({className:e}){return s.jsxs("div",{className:l("pointer-events-none fixed inset-0 z-[1] w-full h-full overflow-hidden bg-transparent","opacity-0 dark:opacity-100 transition-opacity duration-1000 ease-in-out",e),children:[s.jsx("style",{children:`
        @keyframes sl-enter {
          0%   { opacity: 0; transform: translate(-58%,-52%) scale(0.88); }
          100% { opacity: 1; transform: translate(-50%,-42%) scale(1); }
        }
        @keyframes sl-breathe {
          0%,100% { opacity: 1;    transform: translate(-50%,-42%) scale(1);    }
          50%     { opacity: 0.78; transform: translate(-50%,-42%) scale(1.03); }
        }
        @keyframes sl-b2 {
          0%,100% { opacity: 0.5;  transform: translate(-54%,-46%) scale(1);    }
          50%     { opacity: 0.32; transform: translate(-51%,-43%) scale(1.05); }
        }
        @keyframes sl-glow {
          0%,100% { opacity: 0.16; }
          50%     { opacity: 0.09; }
        }
        @keyframes dust-float {
          0%   { opacity: 0;   transform: translate(0px, 0px) scale(0.8); }
          15%  { opacity: 1; }
          85%  { opacity: 1; }
          100% { opacity: 0;   transform: translate(var(--dx), var(--dy)) scale(1.1); }
        }
        .sl-main {
          animation:
            sl-enter   2.2s ease       0.8s  1       forwards,
            sl-breathe 9s   ease-in-out 3s    infinite;
        }
        .sl-beam2 {
          animation:
            sl-enter 2.6s ease       1.1s 1       forwards,
            sl-b2    11s  ease-in-out 3.7s infinite;
        }
        .sl-glow {
          animation:
            sl-enter 3.2s ease       1.4s 1       forwards,
            sl-glow  13s  ease-in-out 4.6s infinite;
        }
        .dust-particle {
          animation: dust-float var(--dur) ease-in-out var(--delay) infinite;
        }
      `}),s.jsxs("svg",{className:`sl-main pointer-events-none absolute z-[3]
                   h-[175%] w-[145%] lg:w-[88%]
                   -top-40 left-0 md:-top-20 md:left-56 opacity-0`,xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 3787 2842",fill:"none",children:[s.jsxs("defs",{children:[s.jsx("filter",{id:"sl-f1",x:"-10%",y:"-10%",width:"120%",height:"120%",filterUnits:"objectBoundingBox",colorInterpolationFilters:"sRGB",children:s.jsx("feGaussianBlur",{stdDeviation:"220"})}),s.jsxs("linearGradient",{id:"sl-g1",x1:"0%",y1:"0%",x2:"100%",y2:"100%",children:[s.jsx("stop",{offset:"0%",stopColor:"#fffbe8",stopOpacity:"0.48"}),s.jsx("stop",{offset:"45%",stopColor:"#ffd27a",stopOpacity:"0.22"}),s.jsx("stop",{offset:"100%",stopColor:"#ff8c2a",stopOpacity:"0.04"})]})]}),s.jsx("g",{filter:"url(#sl-f1)",children:s.jsx("ellipse",{cx:"1924.71",cy:"273.501",rx:"1924.71",ry:"273.501",transform:"matrix(-0.822377 -0.568943 -0.568943 0.822377 3631.88 2291.09)",fill:"url(#sl-g1)"})})]}),s.jsxs("svg",{className:`sl-beam2 pointer-events-none absolute z-[2]
                   h-[165%] w-[135%] lg:w-[80%]
                   -top-32 left-0 md:-top-16 md:left-36 opacity-0`,xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 3787 2842",fill:"none",children:[s.jsxs("defs",{children:[s.jsx("filter",{id:"sl-f2",x:"-10%",y:"-10%",width:"120%",height:"120%",filterUnits:"objectBoundingBox",colorInterpolationFilters:"sRGB",children:s.jsx("feGaussianBlur",{stdDeviation:"280"})}),s.jsxs("linearGradient",{id:"sl-g2",x1:"0%",y1:"0%",x2:"100%",y2:"100%",children:[s.jsx("stop",{offset:"0%",stopColor:"#ffc654",stopOpacity:"0.30"}),s.jsx("stop",{offset:"55%",stopColor:"#ff8030",stopOpacity:"0.12"}),s.jsx("stop",{offset:"100%",stopColor:"#ff5500",stopOpacity:"0.02"})]})]}),s.jsx("g",{filter:"url(#sl-f2)",children:s.jsx("ellipse",{cx:"1924.71",cy:"273.501",rx:"1850",ry:"230",transform:"matrix(-0.800 -0.600 -0.600 0.800 3400 2100)",fill:"url(#sl-g2)"})})]}),s.jsxs("svg",{className:`sl-glow pointer-events-none absolute z-[1]
                   h-[210%] w-[170%] lg:w-[105%]
                   -top-60 -left-20 md:-top-40 md:left-0 opacity-0`,xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 3787 2842",fill:"none",children:[s.jsxs("defs",{children:[s.jsx("filter",{id:"sl-f3",x:"-10%",y:"-10%",width:"120%",height:"120%",filterUnits:"objectBoundingBox",colorInterpolationFilters:"sRGB",children:s.jsx("feGaussianBlur",{stdDeviation:"320"})}),s.jsxs("radialGradient",{id:"sl-g3",cx:"28%",cy:"28%",r:"72%",children:[s.jsx("stop",{offset:"0%",stopColor:"#ffe0a0",stopOpacity:"0.20"}),s.jsx("stop",{offset:"100%",stopColor:"#ff6600",stopOpacity:"0"})]})]}),s.jsx("g",{filter:"url(#sl-f3)",children:s.jsx("ellipse",{cx:"1600",cy:"400",rx:"2200",ry:"380",transform:"matrix(-0.78 -0.62 -0.62 0.78 3200 2400)",fill:"url(#sl-g3)"})})]}),s.jsx("svg",{className:"pointer-events-none absolute z-[4] inset-0 w-full h-full",style:{opacity:0,transition:"opacity 1.5s ease 2.5s",animation:"sl-enter 0.1s ease 2.5s 1 forwards"},xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 100 100",preserveAspectRatio:"xMidYMid slice",children:o.map(t=>s.jsx("circle",{className:"dust-particle",cx:`${t.cx}%`,cy:`${t.cy}%`,r:`${t.r}%`,fill:"#ffefc0",style:{"--dur":`${t.dur}s`,"--delay":`${t.delay+3}s`,"--dx":`${t.dx}%`,"--dy":`${t.dy}%`,opacity:t.opacity,filter:"blur(0.3px)"}},t.id))})]})}export{r as SunlightSpotlight};
