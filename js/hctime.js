/**
 * Created by Jan-Hendrik Caspers
 * Date: 14.07.2018
 * Time: 09:41
 * Home Control (HC) Time input for setting heating, shutter or lighting times on a 24 hours base
 */

$.widget( "jhc.hctimeprogram", {
    options: {
        starttime: 0,
        endtime: 24,
        interval: 15,
        color: '#000000',
        pixel: 5,
        activecolor1: '#ffae00',
        activecolor2: '#ffce63',
        height: 25,
        times: [],
    },
	/* Redraw the TimeBar on Changes */
    reDrawNow: function () {
        if (this._mouseTimer) clearTimeout(this._mouseTimer);
        let per_hour = Math.round(60 / this.options.interval);
        this.pixel_interval = (this.options.pixel+2);
        let length = per_hour * (this.options.endtime - this.options.starttime) * (this.options.pixel + 2);
        /* initialize the SVG image*/
        let bgImage = '<svg width="' + length + '" height="' + this.options.height + '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 ' + length + ' ' + this.options.height + '">';
        /* define the background gradient for active times*/
        bgImage = bgImage + ' <defs>';
        bgImage = bgImage + '  <linearGradient id="grad3" x1="0%" y1="0%" x2="0%" y2="100%">';
        bgImage = bgImage + '  <stop offset="0%" style="stop-color:'+this.options.activecolor1+';stop-opacity:0.5" />';
        bgImage = bgImage + '   <stop offset="100%" style="stop-color:'+this.options.activecolor2+';stop-opacity:0.5" />';
        bgImage = bgImage + '  </linearGradient>';
        bgImage = bgImage + '  </defs>';
        /* Draw one line at the bottom */
        bgImage = bgImage + '<line x1="0" y1="'+(this.options.height-1)+'" x2="'+length+'" y2="'+(this.options.height-1)+'" style="stroke:'+this.options.color+';stroke-width:1" />';
        /* Draw the interval separator vertical lines*/
        let x = 0;
        for (let i=this.options.starttime; i<this.options.endtime; i++) {
            /* long lines at the beginning of an hour */
            bgImage += '<line x1="'+x+'" y1="0" x2="'+x+'" y2="'+(this.options.height-1)+'"  style="stroke:'+this.options.color+';stroke-width:1" />';
            x=x+this.options.pixel+2;
            for (let j=1; j<per_hour; j++) {
                /* shorter lines in between two hours*/
                bgImage += '<line x1="'+x+'" y1="'+(this.options.height-this.options.height*0.8)+'" x2="'+x+'" y2="'+(this.options.height-1)+'"  style="stroke:'+this.options.color+';stroke-width:1" />';
                x=x+this.options.pixel+2;
            }
            bgImage += '<line x1="'+x+'" y1="0" x2="'+x+'" y2="'+(this.options.height-1)+'"  style="stroke:'+this.options.color+';stroke-width:1" />';
        }

        let activePaint = false;
        let paintStart = 0;
        let paintEnd = 0;
        /* draw the active times */
        for (index = 0; index < this.activearray.length; ++index) {
            if (this.activearray[index]==1) {
                if (!activePaint) paintStart = index*this.pixel_interval;
                paintEnd = index*this.pixel_interval;
                activePaint=true;
            } else {
                if (activePaint) {
                    paintEnd = index*this.pixel_interval;
                    bgImage = bgImage + '<rect x="'+paintStart+'" y="0" width="'+(paintEnd-paintStart)+'" height="'+(this.options.height-1)+'" fill="url(#grad3)" />';
                    activePaint=false;
                }
            }
        }
        if (activePaint) {
            bgImage = bgImage + '<rect x="'+paintStart+'" y="0" width="'+(paintEnd-paintStart)+'" height="'+(this.options.height-1)+'" fill="url(#grad3)" />';
        }
        bgImage = bgImage +  '</svg>';
        /* Update the DOM element */
        this.element.css('width',length+'px').css('height',this.options.height+'px').css('padding',0).css('background', "url('data:image/svg+xml,"+encodeURIComponent(bgImage)+"') left no-repeat" ).html('&nbsp;');
    },
    getarray: function() {
        return(this.activearray);
    },
    mouseMove: function(event) {
        if (this._mouseDown) {
            let index = Math.floor(event.offsetX / this.pixel_interval);
            for(var i=(index>this._startOffset?this._startOffset:index);i<=(index<this._startOffset?this._startOffset:index);i++) this.activearray[i] = this._newState;

            let d = new Date();
            this._mouseTimer-=d.getTime();
            if (this._mouseTimer <= 0) {
                this.reDrawNow();
                let d = new Date();
                // put in some delay to reduce flickering
                this._mouseTimer=d.getTime()+100;
            }
        }
    },
    mouseDown: function(event) {
        var index = Math.floor(event.offsetX/this.pixel_interval);
        this._startOffset = Math.floor(event.offsetX/this.pixel_interval);
        if (this.activearray[index]==1) {
            this._newState=0;
        } else this._newState=1;
        this.activearray[index]=this._newState;
        this._mouseDown = true;
    },
    mouseUp: function(event) {
        this._mouseDown = false;
        this.reDrawNow();
        this._mouseTimer=0;
        this._trigger( "selstop", null, { value: 100 } );
    },
    _create: function() {
        this.activearray = [];
        this._mouseDown = false;
        this.pixel_interval = 0;
        this._newState = 0;
        this._mouseTimer = 0;
        this._startOffset = 0;

        let intervalCount = (this.options.endtime - this.options.starttime) * 60 / this.options.interval;
        for (var i=0;i<=intervalCount;i++) this.activearray[i]=0;

        for (var index = 0; index < this.options.times.length; ++index) {
            this.activearray[index]=this.options.times[index];
        }
        this.element.mousedown(function(event) {
            $(this).addClass("hcmousedown");
            $(this).hctimeprogram("mouseDown", event);
        });
        this.element.mouseup(function(event) {
            $(this).removeClass("hcmousedown");
            $(this).hctimeprogram("mouseUp", event);
        });
        this.element.mousemove(function(event) {
            $(this).hctimeprogram("mouseMove", event);
        });
        this.reDrawNow();
    }
});
