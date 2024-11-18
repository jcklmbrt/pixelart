
"use strict";

var /* SaveBox */     savebox;
var /* Palette */     palette;
var /* PixelCanvas */ pixcanv;

const BLACK = 0x00;
const WHITE = 0x3F;
const PIXEL_SIZE = 10;

class PopUp
{
	_elem;

	constructor(elem) 
	{
		this._elem = elem;
	}

	show(x, y)
	{
		if(this._elem.style.display == "block") {
			this._elem.style.display = "none";
		} else {
			this._elem.style.display = "block";
		}

		this._elem.style.left = x.toString(10) + "px";
		this._elem.style.top  = y.toString(10) + "px";
	}

	hide()
	{
		this._elem.style.display = "none";
	}
}

class SaveBox extends PopUp
{
	show(x, y)
	{
		var form_data = document.getElementById("form_data");
		form_data.value = pixcanv.to_base64();

		super.show(x, y);
	}

	constructor()
	{
		var elem = document.getElementsByClassName("savebox")[0];
		super(elem);

		var colorbox = document.getElementById("save");
		colorbox.addEventListener("mousedown", function(e) { 
			savebox.show(e.clientX, e.clientY);
		});
	}
}

class Palette extends PopUp
{
	constructor()
	{
		var elem = document.getElementsByClassName("palette")[0];
		super(elem);

		var colorbox = document.getElementById("color");
		colorbox.addEventListener("mousedown", function(e) { 
			palette.show(e.clientX, e.clientY);
		});
	}
};


class PixelCanvas 
{
	static mouse_down = false;

	#ctx;
	#canvas;
	#pixels;
	#history;
	#future;

	width;
	height;
	tool;

	onchange()
	{
		/* future will be invalid after change */
		this.#future = [];
		var clone = new Uint8Array(this.#pixels);
		this.#history.push(clone);
	}

	check_history()
	{
		if(this.#history.length == 0) {
			return;
		}

		var top = this.#history.pop();

		for(var i = 0; i < this.#pixels.length; i++) {
			if(this.#pixels[i] != top[i]) {
				this.#history.push(top);
				return;
			}
		}

		/* nothing changed, discard our last edit. */
	}

	undo()
	{
		if(this.#history.length != 0) {
			/* transfer ownership of #pixels to #future */
			this.#future.push(this.#pixels);
			var old_pixels = this.#history.pop();
			this.#pixels = new Uint8Array(old_pixels);
		}

		this.draw();
	}

	redo()
	{
		if(this.#future.length != 0) {
			/* transfer ownership of #pixels to #history */
			this.#history.push(this.#pixels);
			var pixels = this.#future.pop();
			this.#pixels = new Uint8Array(pixels);
		}

		this.draw();
	}

	constructor()
	{
		this.#canvas = document.getElementById("canvas");
		this.#ctx    = this.#canvas.getContext("2d");

		this.width  = canvas.width  / PIXEL_SIZE;
		this.height = canvas.height / PIXEL_SIZE;

		this.clear_pixels();

		this.#history = [];
		this.#future  = [];

		this.#canvas.addEventListener("mousemove", function(e) {
			pixcanv.mousemove(e.offsetX, e.offsetY);
		});
	
		this.#canvas.addEventListener("mousedown", function(e) {
			if(e.button == 0 /* Left Button */) {
				pixcanv.mouse_down = true;
				pixcanv.onchange();
				pixcanv.mousemove(e.offsetX, e.offsetY);
			}
		});

		var unsel = function() {
			pixcanv.mouse_down = false;
			pixcanv.check_history();
		}
	
		this.#canvas.addEventListener("mouseup",  unsel);
		this.#canvas.addEventListener("mouseout", unsel);
	}

	mousemove(x, y)
	{
		if(this.mouse_down) {
	
			var pos_x = Math.floor(x / PIXEL_SIZE);
			var pos_y = Math.floor(y / PIXEL_SIZE);
	
			this.tool.interact(this, pos_x, pos_y);	
			this.draw();
		}
	}

	set_cursor(url)
	{
		this.#canvas.style.cursor= "url(" + url + "), auto";
	}

	set_pixel(x, y, color) { this.#pixels[y * this.width + x] = color; }
	get_pixel(x, y) { return this.#pixels[y * this.width + x]; }

	clear_pixels()
	{
		this.#pixels = new Uint8Array(this.width * this.height);

		for(var y = 0; y < this.height; y++)
		for(var x = 0; x < this.width;  x++) {
			this.set_pixel(x, y, WHITE);
		}

		this.#ctx.clearRect(0, 0, canvas.width, canvas.height);
	}

	draw()
	{
		this.#ctx.clearRect(0, 0, canvas.width, canvas.height);

		for(var y = 0; y < this.height; y++)
		for(var x = 0; x < this.width;  x++) {
			var color = this.get_pixel(x, y);

			this.#ctx.fillStyle = color_to_string(color);
			this.#ctx.fillRect(x * PIXEL_SIZE, 
			             y * PIXEL_SIZE, 
			             PIXEL_SIZE, 
			             PIXEL_SIZE);
		}
	}

	to_base64()
	{
		var s = "";
		/* converts to base64 really neatly as each color is already 6 bits. */
		for(var i = 0; i < this.#pixels.length; i++) {
			var pixel = this.#pixels[i] & 0b111111;
			s += String.fromCharCode(pixel + 48);
		}
		return s;
	}
};

class BaseTool
{
	static _color;

	constructor() 
	{
		if(pixcanv.tool) {
			this.set_color(pixcanv.tool._color);
		} else {
			this.set_color(BLACK);
		}
	}

	interact(pixcanv, x, y) 
	{
		palette.hide();
		savebox.hide();
	}

	set_color(color)
	{
		palette.hide();
		savebox.hide();

		var col = document.getElementById("color");
		col.style.background = color_to_string(color);

		this._color = color;
	}
};

class PencilTool extends BaseTool 
{
	constructor()
	{
		pixcanv.set_cursor("img/pencil.png");
		super();
	}

	interact(pixcanv, x, y) /* override */
	{
		pixcanv.set_pixel(x, y, this._color);
		super.interact(pixcanv, x, y);
	}
};

class BucketTool extends BaseTool 
{
	constructor()
	{
		pixcanv.set_cursor("img/bucket.png");
		super();
	}

	interact(pixcanv, x, y) /* override */
	{
		var old_color = pixcanv.get_pixel(x, y);
		var todo      = [x, y];

		if(this._color == old_color) {
			return;
		}

		while(todo.length != 0) {

			y = todo.pop();
			x = todo.pop();

			pixcanv.set_pixel(x, y, this._color);

			if(x + 1 <= pixcanv.width && pixcanv.get_pixel(x + 1, y) == old_color) {
				todo.push(x + 1);
				todo.push(y);
			}
			if(x - 1 >= 0 && pixcanv.get_pixel(x - 1, y) == old_color) {
				todo.push(x - 1);
				todo.push(y);
			}
			if(y + 1 <= pixcanv.height && pixcanv.get_pixel(x, y + 1) == old_color) {
				todo.push(x);
				todo.push(y + 1);
			}
			if(y - 1 >= 0 && pixcanv.get_pixel(x, y - 1) == old_color) {
				todo.push(x);
				todo.push(y - 1);
			}
		}

		super.interact(pixcanv, x, y);
	}
};


class ColorPicker extends BaseTool
{
	constructor()
	{
		pixcanv.set_cursor("img/color-picker.png");
		super();
	}

	interact(pixcanv, x, y) /* override */
	{
		var color = pixcanv.get_pixel(x, y);
		set_tool_color(color);
		super.interact(pixcanv, x, y);
	}
};

/* this is the "API" we expose to the html */
function set_bucket()          { pixcanv.tool = new BucketTool; }
function set_pencil()          { pixcanv.tool = new PencilTool; }
function set_color_picker()    { pixcanv.tool = new ColorPicker; }
function set_tool_color(color) { pixcanv.tool.set_color(color); }
function canvas_reset()        { pixcanv.clear_pixels(); pixcanv.draw(); pixcanv.onchange(); /* in case we want to undo the reset */ }
function canvas_undo()         { pixcanv.undo() }
function canvas_redo()         { pixcanv.redo() }

/* you could use a lookup table instead. it's only 64 values */
function color_to_string(color)
{
	/* color encoding: 0bRRGGBB */
	var blue  = (color >> 0) & 3;
	var green = (color >> 2) & 3;
	var red   = (color >> 4) & 3;

	blue  = (blue  / 3.0) * 255;
	green = (green / 3.0) * 255;
	red   = (red   / 3.0) * 255;

	return "rgb(" + red + "," + green + "," + blue + ")";
}

function main() 
{
	try {
		palette = new Palette;
		pixcanv = new PixelCanvas;
		savebox = new SaveBox; 
	} catch(e) {
		/* no canvas */
		return;
	}

	set_pencil();
	set_tool_color(BLACK);

	/* keybinds */
	window.addEventListener("keydown", function(e) 
	{
		if(e.ctrlKey == true) {
			switch(e.code) {
				case "KeyZ": /* Ctrl-Z */
					canvas_undo();
					break;
				case "KeyY": /* Ctrl-Y */
					canvas_redo();
					break;
			}
		}
	});
}
