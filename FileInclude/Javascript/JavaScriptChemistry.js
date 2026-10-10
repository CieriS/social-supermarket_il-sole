let c, ctx, W, H;
let dots = [];

var effectiveWidth = window.innerWidth; // Width
var effectiveHeight = window.innerHeight; // Height

const random = (max=1, min=0) => Math.random() * (max - min) + min;

class Dot {
    constructor(a){    
        this.x = random(W)
        this.y = random(H)
        this.r = 1.5 // raggio dots
        this.s = { x:random(1.2,-1.2),y:random(1.2,-1.2)} // velocità
        this.dir = {x:1.2,y:1.2} // velocità
    }
    draw() {
        ctx.beginPath()
        ctx.fillStyle = 'white'
        ctx.arc(this.x, this.y, this.r, 0, 2 * Math.PI)
        ctx.fill()
    }
    update() {
        if(this.x>W-this.r||this.x<this.r)this.dir.x*=-1
        if(this.y>H-this.r||this.y<this.r)this.dir.y*=-1
        this.x += this.s.x*this.dir.x
        this.y += this.s.y*this.dir.y
        this.draw()
    }
}

const updateDots = ()=> {
    for(let i=0; i<dots.length; i++){    
        dots[i].update();    
        for(let j=0; j<dots.length; j++){    
            let d = Math.hypot(dots[i].x - dots[j].x,dots[i].y - dots[j].y)
            if(d<60&&i!==j){ // distanza a cui si formano le righine
                ctx.beginPath()
                ctx.strokeStyle = 'rgba(256,256,256,' + 7/d + ')' // cyan 0,255,255
                ctx.lineWidth =  1 // grandezza linee 
                ctx.moveTo(dots[i].x, dots[i].y);
                ctx.lineTo(dots[j].x, dots[j].y);
                ctx.stroke();
            }        
        }  
    }
}

const init = () => {
    c = document.getElementById("cnv");
    c.width = W = window.screen.availWidth;
    c.height = H = window.screen.availHeight;
    ctx = c.getContext("2d");
    for(let i=0;i < ( (effectiveWidth + effectiveHeight )/20 ) ;i++) /* numero di dots */ dots.push(new Dot())
    animate();
};

const animate = () => {
    ctx.clearRect(0, 0, W, H);
    updateDots();
    requestAnimationFrame(animate);
};

    window.onload = init;