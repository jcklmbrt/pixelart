
"use strict";

var pixels;
var canvas;
var ctx;

var mouse_down = false;

const PENCIL       = 0;
const BUCKET       = 1;
const COLOR_PICKER = 2

const BLACK = 0x00;
const WHITE = 0xFF;
const PIXEL_SIZE = 10;

var pen_color = BLACK;
var cur_tool  = PENCIL;

function set_bucket() { canvas.style.cursor= "url(bucket.png),auto"; cur_tool = BUCKET; }
function set_pencil() { canvas.style.cursor= "url(pencil.png),auto"; cur_tool = PENCIL; }
function set_color_picker() { canvas.style.cursor= "url(color-picker.png),auto"; cur_tool = COLOR_PICKER; }

function set_pen_color(color)
{
	var palette = document.getElementsByClassName("palette")[0];
	palette.style.display = "none";

	var col = document.getElementById("color");
	col.style.background = bytecolor_to_string(color);
	pen_color = color;
}

/* you could use a lookup table instead. it's only 256 values */
function bytecolor_to_string(color)
{
	/* color encoding: 0bRRRGGGBB */
	var blue  = (color >> 0) & 3;
	var green = (color >> 2) & 3;
	var red   = (color >> 4) & 3;

	blue  = (blue  / 3.0) * 255;
	green = (green / 3.0) * 255;
	red   = (red   / 3.0) * 255;

	return "rgb(" + red + "," + green + "," + blue + ")";
}

function canvas_reset()
{
	var w = canvas.width  / PIXEL_SIZE;
	var h = canvas.height / PIXEL_SIZE;

	pixels = new Uint8Array(w * h);

	for(var y = 0; y < h; y++)
	for(var x = 0; x < w; x++) {
		pixels[y * w + x] = WHITE;
	}

	ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function flood_fill(x, y)
{
	var w = canvas.width  / PIXEL_SIZE;
	var h = canvas.height / PIXEL_SIZE;

	var old_color = pixels[y * w + x];
	var todo      = [x, y];

	if(pen_color == old_color) {
		return;
	}

	while(todo.length != 0) {

		y = todo.pop();
		x = todo.pop();

		pixels[y * w + x] = pen_color;

		if(x + 1 <= w && pixels[y * w + x + 1] == old_color) {
			todo.push(x + 1);
			todo.push(y);
		}
		if(x - 1 >= 0 && pixels[y * w + x - 1] == old_color) {
			todo.push(x - 1);
			todo.push(y);
		}
		if(y + 1 <= h && pixels[(y + 1) * w + x] == old_color) {
			todo.push(x);
			todo.push(y + 1);
		}
		if(y - 1 >= 0 && pixels[(y - 1) * w + x] == old_color) {
			todo.push(x);
			todo.push(y - 1);
		}
	}
}

function canvas_mousemove(e) 
{
	if(mouse_down) {
		var pos_x = Math.floor(e.offsetX / PIXEL_SIZE);
		var pos_y = Math.floor(e.offsetY / PIXEL_SIZE);

		var w = canvas.width  / PIXEL_SIZE;
		var h = canvas.height / PIXEL_SIZE;

		switch(cur_tool) {
		case BUCKET:
			flood_fill(pos_x, pos_y);
			break;
		case PENCIL:
			pixels[pos_y * w + pos_x] = pen_color;
			break;
		case COLOR_PICKER:
			set_pen_color(pixels[pos_y * w + pos_x]);
			break;
		}

		for(var y = 0; y < h; y++)
		for(var x = 0; x < w; x++) {
			var color = pixels[y * w + x];

			ctx.fillStyle = bytecolor_to_string(color);
			ctx.fillRect(x * PIXEL_SIZE, y * PIXEL_SIZE, PIXEL_SIZE, PIXEL_SIZE);
		}
	}
}

function show_palette(e)
{
	var palette = document.getElementsByClassName("palette")[0];

	if(palette.style.display == "block") {
		palette.style.display = "none";
	} else {
		palette.style.display = "block";
	}

	palette.style.left = e.clientX.toString(10) + "px";
	palette.style.top  = e.clientY.toString(10) + "px";
}

function canvas_mousedown(e)
{
	console.log(e);
	mouse_down = true;
	canvas_mousemove(e);
}

function canvas_unsel(e)
{
	mouse_down = false;
}


function main() 
{
	canvas = document.getElementById("canvas");
	ctx    = canvas.getContext("2d");

	/* init pixels */
	canvas_reset();

	/* start with black pencil */
	set_pencil();
	set_pen_color(BLACK);

	var colorbox = document.getElementById("color");
	colorbox.addEventListener("mousedown", show_palette);
	
	canvas.addEventListener("mousemove", canvas_mousemove);
	canvas.addEventListener("mousedown", canvas_mousedown);
	canvas.addEventListener("mouseup",   canvas_unsel);
	canvas.addEventListener("mouseout",  canvas_unsel);
}