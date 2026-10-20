import{c as i,j as e,X as f,U as g,L as k,C as w,e as v}from"./index-CFFIX-xn.js";import{b as r}from"./react-vendor-D7WUTLsu.js";import{S as j}from"./star-D6b2-aOR.js";const N=i("Angry",[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M16 16s-1.5-2-4-2-4 2-4 2",key:"epbg0q"}],["path",{d:"M7.5 8 10 9",key:"olxxln"}],["path",{d:"m14 9 2.5-1",key:"1j6cij"}],["path",{d:"M9 10h0",key:"1vxvly"}],["path",{d:"M15 10h0",key:"1j6oav"}]]),S=i("Frown",[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M16 16s-1.5-2-4-2-4 2-4 2",key:"epbg0q"}],["line",{x1:"9",x2:"9.01",y1:"9",y2:"9",key:"yxxnd0"}],["line",{x1:"15",x2:"15.01",y1:"9",y2:"9",key:"1p4y9e"}]]),I=i("Meh",[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["line",{x1:"8",x2:"16",y1:"15",y2:"15",key:"1xb1d9"}],["line",{x1:"9",x2:"9.01",y1:"9",y2:"9",key:"yxxnd0"}],["line",{x1:"15",x2:"15.01",y1:"9",y2:"9",key:"1p4y9e"}]]),z=i("Smile",[["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}],["path",{d:"M8 14s1.5 2 4 2 4-2 4-2",key:"1y1vjs"}],["line",{x1:"9",x2:"9.01",y1:"9",y2:"9",key:"yxxnd0"}],["line",{x1:"15",x2:"15.01",y1:"9",y2:"9",key:"1p4y9e"}]]),M={1:{color:"#ff4757",shadow:"rgba(255, 71, 87, 0.4)",label:"Terrible",Icon:N},2:{color:"#ffa502",shadow:"rgba(255, 165, 2, 0.4)",label:"Bad",Icon:S},3:{color:"#ffd32a",shadow:"rgba(255, 211, 42, 0.4)",label:"Okay",Icon:I},4:{color:"#00b894",shadow:"rgba(0, 184, 148, 0.4)",label:"Good",Icon:z},5:{color:"#6c5ce7",shadow:"rgba(108, 92, 231, 0.4)",label:"Excellent!",Icon:j}},Y=({onSubmit:c})=>{const[n,p]=r.useState(50),[o,b]=r.useState(""),[h,l]=r.useState(!1),[d,u]=r.useState(!1),[m,x]=r.useState(!1);r.useEffect(()=>{const t=setTimeout(()=>{localStorage.getItem("feedback_submitted")||l(!0)},3e5);return()=>clearTimeout(t)},[]);const s=(t=>t>=80?5:t>=60?4:t>=40?3:t>=20?2:1)(n),a=M[s],y=async()=>{if(!o.trim())return alert("Please enter your name!");x(!0);try{await v("feedback",{name:o,rating:s,text:a.label}),u(!0),localStorage.setItem("feedback_submitted","true"),c&&c({name:o,rating:s,label:a.label}),setTimeout(()=>l(!1),2500)}catch(t){console.error("Error saving feedback:",t),alert("Something went wrong! Please try again.")}x(!1)};return h?e.jsxs("div",{className:"fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm animate-in fade-in duration-300",children:[e.jsx("style",{children:`
        .feedback-card {
          background: #ffffff;
          width: 100%;
          max-width: 400px;
          border-radius: 24px;
          padding: 32px;
          text-align: center;
          box-shadow: 0 10px 30px rgba(0,0,0,0.1);
          transition: all 0.3s ease;
        }
        .dark .feedback-card {
          background: #1e293b;
          box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .icon-wrapper {
          position: relative;
          width: 100px;
          height: 100px;
          margin: 0 auto 20px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .icon-glow {
          position: absolute;
          width: 100%;
          height: 100%;
          border-radius: 50%;
          background: var(--theme-color);
          filter: blur(20px);
          opacity: 0.3;
          animation: pulse 2s infinite;
        }
        .icon-stage {
          position: relative;
          z-index: 10;
          color: var(--theme-color);
          filter: drop-shadow(0 5px 15px var(--theme-shadow));
        }
        .mood-label {
          color: var(--theme-color);
          font-weight: 800;
          text-transform: uppercase;
          letter-spacing: 2px;
          font-size: 14px;
          margin-bottom: 24px;
        }
        .slider-container input[type="range"] {
          -webkit-appearance: none;
          width: 100%;
          height: 8px;
          border-radius: 4px;
          background: #e2e8f0;
          outline: none;
          background-image: linear-gradient(to right, var(--theme-color), var(--theme-color));
          background-repeat: no-repeat;
          margin-bottom: 24px;
        }
        .dark .slider-container input[type="range"] {
          background: #334155;
        }
        .slider-container input[type="range"]::-webkit-slider-thumb {
          -webkit-appearance: none;
          width: 24px;
          height: 24px;
          background: #fff;
          border: 4px solid var(--theme-color);
          border-radius: 50%;
          cursor: pointer;
          box-shadow: 0 0 10px var(--theme-shadow);
          transition: transform 0.1s;
        }
        .slider-container input[type="range"]::-webkit-slider-thumb:hover {
          transform: scale(1.2);
        }
        .submit-btn {
          width: 100%;
          padding: 14px;
          border-radius: 12px;
          border: none;
          background: var(--theme-color);
          color: white;
          font-weight: bold;
          font-size: 16px;
          cursor: pointer;
          box-shadow: 0 5px 15px var(--theme-shadow);
          transition: transform 0.2s, box-shadow 0.2s;
        }
        .submit-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 20px var(--theme-shadow);
        }
        .submit-btn:active {
          transform: translateY(0);
        }
        .pop-anim {
          animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn {
          from { transform: scale(0); opacity: 0; }
          to { transform: scale(1); opacity: 1; }
        }
        @keyframes pulse {
          0% { transform: scale(0.8); opacity: 0.3; }
          50% { transform: scale(1.1); opacity: 0.1; }
          100% { transform: scale(0.8); opacity: 0.3; }
        }
        /* Success Overlay Styles */
        .success-overlay {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 20px;
          min-height: 300px; /* Fixed height to match form view */
        }
      `}),e.jsxs("div",{className:"relative mx-4 feedback-card dark",style:{"--theme-color":a.color,"--theme-shadow":a.shadow},children:[!d&&e.jsx("button",{onClick:()=>l(!1),className:"absolute transition-colors top-4 right-4 text-slate-400 hover:text-red-500",children:e.jsx(f,{size:24})}),d?e.jsxs("div",{className:"duration-300 success-overlay animate-in fade-in zoom-in",children:[e.jsx("div",{style:{color:"#00b894",marginBottom:"20px"},children:e.jsx(w,{size:80,className:"pop-anim drop-shadow-lg"})}),e.jsx("h2",{className:"mb-2 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600",children:"Thank You!"}),e.jsx("p",{className:"text-lg font-medium text-slate-500 dark:text-slate-400",children:"Your feedback helps us grow."})]}):e.jsxs("div",{className:"form-view",children:[e.jsxs("div",{className:"icon-wrapper",children:[e.jsx("div",{className:"icon-glow"}),e.jsx("div",{className:"icon-stage",children:e.jsx(a.Icon,{size:80,className:"pop-anim"},s)})]}),e.jsx("h2",{className:"mb-1 text-2xl font-bold text-slate-800 dark:text-white",children:"Feedback Time!"}),e.jsx("div",{className:"mood-label",children:a.label}),e.jsxs("div",{className:"relative mb-6",children:[e.jsx(g,{className:"absolute -translate-y-1/2 left-3 top-1/2 text-slate-400",size:18}),e.jsx("input",{type:"text",placeholder:"Enter your name",value:o,onChange:t=>b(t.target.value),className:"w-full pl-10 pr-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-700/50 border-none outline-none focus:ring-2 focus:ring-[var(--theme-color)] transition-all dark:text-white placeholder-slate-400"})]}),e.jsx("div",{className:"slider-container",children:e.jsx("input",{type:"range",min:"0",max:"100",value:n,onChange:t=>p(parseInt(t.target.value)),style:{backgroundSize:`${n}% 100%`}})}),e.jsx("button",{type:"button",className:"flex items-center justify-center gap-2 submit-btn disabled:opacity-70 disabled:cursor-not-allowed",onClick:y,disabled:m,children:m?e.jsx(k,{className:"animate-spin",size:20}):"Share Feedback"})]})]})]}):null};export{Y as default};
