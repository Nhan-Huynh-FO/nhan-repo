(function (lib, img, cjs) {

var p; // shortcut to reference prototypes

// stage content:
(lib.stadium_map = function(mode,startPosition,loop) {
if (loop == null) { loop = false; }	this.initialize(mode,startPosition,loop,{},true);

	// svd details
	this.natal_detail = new lib.natal_detail_mc();
	this.natal_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.natal_detail.alpha = 0;

	this.rio_detail = new lib.rio_detail_mc();
	this.rio_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.rio_detail.alpha = 0;

	this.brasilia_detail = new lib.brasilia_detail_mc();
	this.brasilia_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.brasilia_detail.alpha = 0;

	this.cuiaba_detail = new lib.cuiaba_detail_mc();
	this.cuiaba_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.cuiaba_detail.alpha = 0;

	this.manaus_detail = new lib.manaus_detail_mc();
	this.manaus_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.manaus_detail.alpha = 0;

	this.sao_detail = new lib.sao_detail_mc();
	this.sao_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.sao_detail.alpha = 0;

	this.curitiba_detail = new lib.curitiba_detail_mc();
	this.curitiba_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.curitiba_detail.alpha = 0;

	this.recife_detail = new lib.recife_detail_mc();
	this.recife_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.recife_detail.alpha = 0;

	this.porto_detail = new lib.porto_detail_mc();
	this.porto_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.porto_detail.alpha = 0;

	this.fortaleza_detail = new lib.fortaleza_detail_mc();
	this.fortaleza_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.fortaleza_detail.alpha = 0;

	this.salvador_detail = new lib.salvador_detail_mc();
	this.salvador_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.salvador_detail.alpha = 0;

	this.belo_detail = new lib.belo_detail_mc();
	this.belo_detail.setTransform(490,800,1,1,0,0,0,490,800);
	this.belo_detail.alpha = 0;

	this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.belo_detail},{t:this.salvador_detail},{t:this.fortaleza_detail},{t:this.porto_detail},{t:this.recife_detail},{t:this.curitiba_detail},{t:this.sao_detail},{t:this.manaus_detail},{t:this.cuiaba_detail},{t:this.brasilia_detail},{t:this.rio_detail},{t:this.natal_detail}]}).wait(153));

	// title1
	this.title1 = new lib.title_1_mc();
	this.title1.setTransform(488.8,63.4,1.5,1.5,0,0,0,238.8,26.5);
	this.title1.alpha = 0;
	this.title1._off = true;

	this.timeline.addTween(cjs.Tween.get(this.title1).wait(23).to({_off:false},0).to({regX:238.7,scaleX:0.9,scaleY:0.9,x:488.7,alpha:1},10).to({regX:238.8,scaleX:1.05,scaleY:1.05,x:488.8},2).to({regY:26.6,scaleX:0.95,scaleY:0.95,y:63.5},2).to({regY:26.5,scaleX:1,scaleY:1,y:63.4},2).wait(114));

	// title2
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#FFFFFF").s().p("AavmlQhwAAgegdQgegdAAhgQAAgqAKgKQASgZAwgLQAygMAoAAQAnAAAaAEQAZAEAbAMQAdAOAMAfQAOAhAAAxQAABfg5AEQhXAIgWAAIAAAAEAgDgJvQAMgMAsgVQAsgTA4AAQCUAAAABSIAAAIQgREAAADGQAAIeBnAMIAyABQgcCZguAwQguAwheAAQhGAAhAgsQhEgsgfh9Qggh9AAhkIAXtGQAGgJAKgLIAAAAAdnEZQgSDcgWA+QgQAygbAYQgcAagzAAQg2AAgxgRQgxgRgFgUQgojAAApSIAAh2QAAgEAAgBQAAgBAFgIQAEgJAHgFQAHgEAPgEQARgEAXAAQBjAADBA7QAHBJAABXQAACxgSDcIAAAAAovmHQAABWgkAqQgEACgYAMQgZALgNAJQgOAIgdASQgeASgSAOQgUANgcAZQgdAXgSAXQgSAVgVAgQgVAfgNAeQgNAggJAmQgHAnAAAqQAAAzAUCmQAUCmAAAwQAAAbgMAJQgoAciaAAQgEAAgFAAQgFAAgRgEQgPgEgMgGQgMgGgMgOQgMgMgEgSQhunqAAkKQAAiUAYhvQCAATBYBFQEahSCIAAQA7AAAvAJIAAAAAAAmHQBBgSAfgFQAhgFAIAAQA4AwAxBMQAxBMApBnQApBnAVA+QAXBCAiBtQAdBlAZBqQAYBsAKA5IAIA7QgGACgMAAQiiAAiEiCQjACIjUAAQieAAhqhgQhqhhAAjOQAAiAAmhpQAmhpA5hEQA5hFBKg1QBMg2BEgbQBDgcBAgSIAAAAA4hIsQhOAthhAcQhhAchLAKQhLAJg9AAQiAAAhbhDQhchEgshtQgshrgUhoQgShmAAhmQAAiOAwilQAvilAwhcIAzheQAdACAwAGQAxAGB7AcQB7AbBfAnQBfAmBNBKQBNBKAABaQAABQhcBQQEgCOAADeQAABgg2BLQg2BKhOAsIAAAAAkQD1QAAA0AjAcQAiAcAyAAQA2AAAtgjQAtgjAAg4QAAgzgsggQgsghg4AAQgyAAgiAhQgjAgAABFIAAAAASvlsQBOAbA0AzQA0AyAABDQAAAygeAkIgYAcQgaAZgeAkQgeAlggAtQgeAsgVA1QgVA0AAAsQAAA6AdAqQAfAqAwATQAvATArAHQArAJAqAAQAJAAARgBQAQgCANAAQAdAAAAARQgEBQiAAxQh/AxiIAAQhxAAhYgNQhZgNg3gTQg4gTgogdQgogdgRgXQgSgXgLghQgJgfgBgOQgBgMAAgVQAAgEAAgCQAAg9AohCQAohCA5g1QA7gzA5gwQA5guAngqQAogqAAgcQAAhSkPAAQhlAAgfADQAAgFANgWQAMgWAfgiQAeggAngeQAngeA/gVQA/gVBFAAQA9AABIAMQBIAOBOAZIAAAAEgiWgDhQAAA6AiAhQAhAhA6AAQA9AAAxgkQAwgkAAg9QAAg3gugkQgugjg9AAQg1AAgmAiQgnAhAABEIAAAAA/NENQAAA8AhAhQAgAhA9AAQA8AAAuglQAvglAAg9QAAg1gvglQgugmg6AAQg2AAglAjQglAiAABEIAAAA").cp();
	this.shape.setTransform(485.8,165.9);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#FFFFFF").s().p("AbqqfQAZAEAbAMQAdAOAMAfQAOAhAAAxQAABfg5AEQhXAIgWAAQhwAAgegdQgegdAAhgQAAgqAKgKQASgZAwgLQAygMAoAAQAnAAAaAEIAAAAEAg7gKQQAsgTA4AAQCUAAAABSIAAAIQgREAAADGQAAIeBnAMIAyABQgcCZguAwQguAwheAAQhGAAhAgsQhEgsgfh9Qggh9AAhkIAXtGQAGgJAKgLQAMgMAsgVIAAAAAdnEZQgSDcgWA+QgQAygbAYQgcAagzAAQg2AAgxgRQgxgRgFgUQgojAAApSIAAh2QAAgEAAgBQAAgBAFgIQAEgJAHgFQAHgEAPgEQARgEAXAAQBjAADBA7QAHBJAABXQAACxgSDcIAAAAAItjGQAAgFANgWQAMgWAfgiQAeggAngeQAngeA/gVQA/gVBFAAQA9AABIAMQBIAOBOAZQBOAbA0AzQA0AyAABDQAAAygeAkIgYAcQgaAZgeAkQgeAlggAtQgeAsgVA1QgVA0AAAsQAAA6AdAqQAfAqAwATQAvATArAHQArAJAqAAQAJAAARgBQAQgCANAAQAdAAAAARQgEBQiAAxQh/AxiIAAQhxAAhYgNQhZgNg3gTQg4gTgogdQgogdgRgXQgSgXgLghQgJgfgBgOQgBgMAAgVQAAgEAAgCQAAg9AohCQAohCA5g1QA7gzA5gwQA5guAngqQAogqAAgcQAAhSkPAAQhlAAgfADIAAAAACJmjQA4AwAxBMQAxBMApBnQApBnAVA+QAXBCAiBtQAdBlAZBqQAYBsAKA5IAIA7QgGACgMAAQiiAAiEiCQjACIjUAAQieAAhqhgQhqhhAAjOQAAiAAmhpQAmhpA5hEQA5hFBKg1QBMg2BEgbQBDgcBAgSQBBgSAfgFQAhgFAIAAIAAAAAt2giQgVAfgNAeQgNAggJAmQgHAnAAAqQAAAzAUCmQAUCmAAAwQAAAbgMAJQgoAciaAAQgEAAgFAAQgFAAgRgEQgPgEgMgGQgMgGgMgOQgMgMgEgSQhunqAAkKQAAiUAYhvQCAATBYBFQEahSCIAAQA7AAAvAJQAABWgkAqQgEACgYAMQgZALgNAJQgOAIgdASQgeASgSAOQgUANgcAZQgdAXgSAXQgSAVgVAgIAAAAAgJDjQAAgzgsggQgsghg4AAQgyAAgiAhQgjAgAABFQAAA0AjAcQAiAcAyAAQA2AAAtgjQAtgjAAg4IAAAAA4hIsQhOAthhAcQhhAchLAKQhLAJg9AAQiAAAhbhDQhchEgshtQgshrgUhoQgShmAAhmQAAiOAwilQAvilAwhcIAzheQAdACAwAGQAxAGB7AcQB7AbBfAnQBfAmBNBKQBNBKAABaQAABQhcBQQEgCOAADeQAABgg2BLQg2BKhOAsIAAAAA+plFQgugjg9AAQg1AAgmAiQgnAhAABEQAAA6AiAhQAhAhA6AAQA9AAAxgkQAwgkAAg9QAAg3gugkIAAAAA7lCqQgugmg6AAQg2AAglAjQglAiAABEQAAA8AhAhQAgAhA9AAQA8AAAuglQAvglAAg9QAAg1gvglIAAAA").cp();
	this.shape_1.setTransform(485.8,165.9);

	this.timeline.addTween(cjs.Tween.get({}).to({state:[]}).to({state:[{t:this.shape}]},39).to({state:[]},2).to({state:[{t:this.shape_1}]},3).to({state:[]},2).to({state:[{t:this.shape_1}]},3).wait(104));

	// title3
	this.title3 = new lib.title_2_mc();
	this.title3.setTransform(612.7,317.3,2,2,0,0,0,134.8,69.2);
	this.title3.alpha = 0;
	this.title3._off = true;

	this.timeline.addTween(cjs.Tween.get(this.title3).wait(49).to({_off:false},0).to({scaleX:0.9,scaleY:0.9,x:612.6,y:317.4,alpha:1},7).to({scaleX:1.05,scaleY:1.05,x:612.7,y:317.3},2).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1,scaleY:1},2).wait(91));

	// svd_list_hover
	this.recife_hover = new lib.recife_hover_mc();
	this.recife_hover.setTransform(820.5,794.2,0.5,0.5,0,0,0,120.5,120.5);
	this.recife_hover.alpha = 0;

	this.salvador_hover = new lib.salvador_hover_mc();
	this.salvador_hover.setTransform(725.5,878.2,0.5,0.5,0,0,0,120.5,120.5);
	this.salvador_hover.alpha = 0;

	this.belo_hover = new lib.belo_hover_mc();
	this.belo_hover.setTransform(589.5,1038.4,0.5,0.5,0,0,0,120.5,120.5);
	this.belo_hover.alpha = 0;

	this.rio_hover = new lib.rio_hover_mc();
	this.rio_hover.setTransform(646,1153.4,0.5,0.5,0,0,0,120.5,120.5);
	this.rio_hover.alpha = 0;

	this.sao_hover = new lib.sao_paulo_hover_mc();
	this.sao_hover.setTransform(531.1,1196.3,0.5,0.5,0,0,0,120.5,120.5);
	this.sao_hover.alpha = 0;

	this.porto_hover = new lib.porto_alegre_hover_mc();
	this.porto_hover.setTransform(350.6,1382.3,0.5,0.5,0,0,0,120.5,120.5);
	this.porto_hover.alpha = 0;

	this.curitiba_hover = new lib.curitiba_hover_mc();
	this.curitiba_hover.setTransform(412.1,1249.4,0.5,0.5,0,0,0,120.5,120.5);
	this.curitiba_hover.alpha = 0;

	this.brasila_hover = new lib.braslia_hover_mc();
	this.brasila_hover.setTransform(484.3,948.1,0.5,0.5,0,0,0,120.5,120.5);
	this.brasila_hover.alpha = 0;

	this.cuiaba_hover = new lib.cuiaba_hover_mc();
	this.cuiaba_hover.setTransform(315.6,932.7,0.5,0.5,0,0,0,120.5,120.5);
	this.cuiaba_hover.alpha = 0;

	this.natal_hover = new lib.natal_hover_mc();
	this.natal_hover.setTransform(830,669.7,0.5,0.5,0,0,0,120.5,120.5);
	this.natal_hover.alpha = 0;

	this.fortaleza_hover = new lib.fortaleza_hover_mc();
	this.fortaleza_hover.setTransform(712.6,620.1,0.5,0.5,0,0,0,120.5,120.5);
	this.fortaleza_hover.alpha = 0;

	this.manaus_hover = new lib.manaus_hover_mc();
	this.manaus_hover.setTransform(161.6,607.2,0.5,0.5);
	this.manaus_hover.alpha = 0;

	this.timeline.addTween(cjs.Tween.get({}).to({state:[]}).to({state:[{t:this.manaus_hover},{t:this.fortaleza_hover},{t:this.natal_hover},{t:this.cuiaba_hover},{t:this.brasila_hover},{t:this.curitiba_hover},{t:this.porto_hover},{t:this.sao_hover},{t:this.rio_hover},{t:this.belo_hover},{t:this.salvador_hover},{t:this.recife_hover}]},62).wait(91));

	// manaus
	this.manaus = new lib.manaus_normal_mc();
	this.manaus.setTransform(162.6,607.6,0.6,0.6,0,0,0,60.5,60.6);
	this.manaus.alpha = 0;
	this.manaus._off = true;

	this.timeline.addTween(cjs.Tween.get(this.manaus).wait(62).to({_off:false},0).to({regX:60.6,scaleX:1.1,scaleY:1.1,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(78));

	// fortaleza
	this.fortaleza = new lib.fortaleza_normal_mc();
	this.fortaleza.setTransform(713.2,620.2,0.6,0.6,0,0,0,60.1,60);
	this.fortaleza.alpha = 0;
	this.fortaleza._off = true;

	this.timeline.addTween(cjs.Tween.get(this.fortaleza).wait(69).to({_off:false},0).to({scaleX:1.1,scaleY:1.1,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(71));

	// natal
	this.natal = new lib.natal_normal_mc();
	this.natal.setTransform(829.7,670.2,0.6,0.6,0,0,0,60,60.5);
	this.natal.alpha = 0;
	this.natal._off = true;

	this.timeline.addTween(cjs.Tween.get(this.natal).wait(76).to({_off:false},0).to({regY:60.6,scaleX:1.1,scaleY:1.1,y:670.3,alpha:1},7).to({scaleX:0.95,scaleY:0.95,y:670.2},2).to({regY:60.5,scaleX:1.05,scaleY:1.05,x:829.8},2).to({regY:60.6,scaleX:1,scaleY:1,x:829.7,y:670.3},2).wait(64));

	// recife
	this.recife = new lib.recife_normal_mc();
	this.recife.setTransform(821.2,794.7,0.6,0.6,0,0,0,60.1,60);
	this.recife.alpha = 0;
	this.recife._off = true;

	this.timeline.addTween(cjs.Tween.get(this.recife).wait(83).to({_off:false},0).to({regY:60.1,scaleX:1.1,scaleY:1.1,x:821.3,y:794.8,alpha:1},7).to({scaleX:1,scaleY:1},2).to({scaleX:1.05,scaleY:1.05,y:794.7},2).to({scaleX:1,scaleY:1,y:794.8},2).wait(57));

	// salvador
	this.salvador = new lib.salvador_normal_mc();
	this.salvador.setTransform(726.8,878.3,0.6,0.6,0,0,0,60.3,60);
	this.salvador.alpha = 0;
	this.salvador._off = true;

	this.timeline.addTween(cjs.Tween.get(this.salvador).wait(90).to({_off:false},0).to({scaleX:1.1,scaleY:1.1,alpha:1},7).to({regX:60.2,scaleX:0.95,scaleY:0.95,x:726.7},2).to({regX:60.3,scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1,x:726.8},2).wait(50));

	// cuiaba
	this.cuiaba = new lib.cuiaba_normal_mc();
	this.cuiaba.setTransform(316.6,933.3,0.6,0.6,0,0,0,60.5,60.1);
	this.cuiaba.alpha = 0;
	this.cuiaba._off = true;

	this.timeline.addTween(cjs.Tween.get(this.cuiaba).wait(97).to({_off:false},0).to({regX:60.6,scaleX:1.1,scaleY:1.1,x:316.7,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(43));

	// brasila
	this.brasila = new lib.brasila_normal_mc();
	this.brasila.setTransform(484.7,947.8,0.6,0.6,0,0,0,60.5,60);
	this.brasila.alpha = 0;
	this.brasila._off = true;

	this.timeline.addTween(cjs.Tween.get(this.brasila).wait(104).to({_off:false},0).to({scaleX:1.1,scaleY:1.1,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05,x:484.6},2).to({scaleX:1,scaleY:1,x:484.7},2).wait(36));

	// belo
	this.belo = new lib.belo_normal_mc();
	this.belo.setTransform(590.2,1039.3,0.6,0.6,0,0,0,60,60.5);
	this.belo.alpha = 0;
	this.belo._off = true;

	this.timeline.addTween(cjs.Tween.get(this.belo).wait(111).to({_off:false},0).to({scaleX:1.1,scaleY:1.1,alpha:1},7).to({regY:60.6,scaleX:0.95,scaleY:0.95},2).to({regY:60.5,scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(29));

	// rio
	this.rio = new lib.rio_normal_mc();
	this.rio.setTransform(646.2,1152.8,0.6,0.6,0,0,0,59.9,60.5);
	this.rio.alpha = 0;
	this.rio._off = true;

	this.timeline.addTween(cjs.Tween.get(this.rio).wait(118).to({_off:false},0).to({regY:60.6,scaleX:1.1,scaleY:1.1,y:1152.9,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(22));

	// sao paulo
	this.sao = new lib.sao_paulo_normal_mc();
	this.sao.setTransform(530.7,1195.8,0.6,0.6,0,0,0,59.6,60.1);
	this.sao.alpha = 0;
	this.sao._off = true;

	this.timeline.addTween(cjs.Tween.get(this.sao).wait(125).to({_off:false},0).to({scaleX:1.1,scaleY:1.1,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05},2).to({scaleX:1,scaleY:1},2).wait(15));

	// curitiba
	this.curitiba = new lib.curitiba_normal_mc();
	this.curitiba.setTransform(413.1,1250.3,0.6,0.6,0,0,0,60.5,60.5);
	this.curitiba.alpha = 0;
	this.curitiba._off = true;

	this.timeline.addTween(cjs.Tween.get(this.curitiba).wait(132).to({_off:false},0).to({regX:60.6,scaleX:1.1,scaleY:1.1,x:413.2,alpha:1},7).to({regY:60.6,scaleX:0.95,scaleY:0.95,x:413.1,y:1250.4},2).to({regY:60.5,scaleX:1.05,scaleY:1.05,x:413.2,y:1250.3},2).to({scaleX:1,scaleY:1},2).wait(8));

	// porto alegre
	this.porto = new lib.porto_alegre_normal_mc();
	this.porto.setTransform(351.1,1382.9,0.6,0.6,0,0,0,60,60.5);
	this.porto.alpha = 0;
	this.porto._off = true;

	this.timeline.addTween(cjs.Tween.get(this.porto).wait(139).to({_off:false},0).to({regX:60.1,scaleX:1.1,scaleY:1.1,x:351.2,alpha:1},7).to({scaleX:0.95,scaleY:0.95},2).to({scaleX:1.05,scaleY:1.05,y:1382.8},2).to({scaleX:1,scaleY:1,y:1382.9},2).wait(1));

	// background
	this.instance = new lib.bg_mc();
	this.instance.setTransform(490,800,1.5,1.5,0,0,0,490,800);
	this.instance.alpha = 0;

	this.timeline.addTween(cjs.Tween.get(this.instance).to({scaleX:1,scaleY:1,alpha:1},23).wait(130));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(-244.9,-399.9,1470,2400);


// symbols:
(lib.ArenaAmazonia = function() {
	this.initialize(img.ArenaAmazonia);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.ArenaCorinthians = function() {
	this.initialize(img.ArenaCorinthians);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.ArenadaBaixda = function() {
	this.initialize(img.ArenadaBaixda);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.ArenadasDunas = function() {
	this.initialize(img.ArenadasDunas);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.ArenaPernambuco = function() {
	this.initialize(img.ArenaPernambuco);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.ban_do_svd = function() {
	this.initialize(img.ban_do_svd);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.belo_item = function() {
	this.initialize(img.belo_item);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,121,121);


(lib.BeloHorizonte = function() {
	this.initialize(img.BeloHorizonte);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Brasilia = function() {
	this.initialize(img.Brasilia);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Cuiaba = function() {
	this.initialize(img.Cuiaba);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Curitiba = function() {
	this.initialize(img.Curitiba);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.EstadioBeiraRio_PortoAlegre = function() {
	this.initialize(img.EstadioBeiraRio_PortoAlegre);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.EstadioCastelao = function() {
	this.initialize(img.EstadioCastelao);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.FonteNova = function() {
	this.initialize(img.FonteNova);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.Fortaleza = function() {
	this.initialize(img.Fortaleza);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Manaus = function() {
	this.initialize(img.Manaus);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.ManeGarrincha = function() {
	this.initialize(img.ManeGarrincha);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.Maracana = function() {
	this.initialize(img.Maracana);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.Mineirao = function() {
	this.initialize(img.Mineirao);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.Natal = function() {
	this.initialize(img.Natal);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Pantanal = function() {
	this.initialize(img.Pantanal);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.PortoAlegre = function() {
	this.initialize(img.PortoAlegre);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Recife = function() {
	this.initialize(img.Recife);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.rio_item = function() {
	this.initialize(img.rio_item);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,121,121);


(lib.RiodeJaneiro = function() {
	this.initialize(img.RiodeJaneiro);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.Salvador = function() {
	this.initialize(img.Salvador);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.SaoPaulo = function() {
	this.initialize(img.SaoPaulo);
}).prototype = new cjs.Bitmap();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.vene_mc = function() {
	this.initialize();

	// Layer 3
	this.shape = new cjs.Shape();
	this.shape.graphics.f("#F4EA08").s().p("AikCWQgugRgqgbQADgbAFgcQArjcDAgOQAEgBAFAAQAAAAABAAQA7gCAxASQACABADABQAeAMAaAVQAWASARAWQABACACACQAcAnAJAyQALA7gTAyQgHARgLARQgEAGgEAGQhWAehhAAQgrAAgqgGQgMgNgIgQQgQggADglQADgkAUgWQAmgnAxAQQAyAQgFAoQgHA+gtAJQgOACgOgDIgOgDQANAdAfAKQAgAKAigMQBWgegChoQgChuhngaQgsgLgrALQguALggAfQg1AzgIB9QgCAWAAAUIAAAA").cp();
	this.shape.setTransform(51.8,91.4);

	this.shape_1 = new cjs.Shape();
	this.shape_1.graphics.f("#057A3E").s().p("AgTAMQgWgZgTgbQALAAALABQALABALABQAHARAPAbQAJAQAJAPQgDABgEACQgGgGgHgFQgNgMgKgGIAAAAAAngfQAKAWAMATQgTgUgTgUQgCgCgCgCQAKABAKACIAAAA").cp();
	this.shape_1.setTransform(14.9,89.1);

	this.shape_2 = new cjs.Shape();
	this.shape_2.graphics.f("#066F3E").s().p("ABPhZQhPhFh2gaQgGgBgFgBQAfgvAqgrQALgLALgKQBOAbBMA5QAGAGAHAGQAOALANAMQATARASASQAAAXAEAgQAHA0AKAxQgcgogggoQg/hRgPAIQgLAFAKAuIAAAAAiSAoQghgXgfAEQAJg5AWg3QAjAEAmASQApAUAeAgQAJAVASAiQAMAaAPAZQACALABAMQgegWgggUQhZg4gPANQgEAEACAJIAAAAAjYCIQgBgMAAgNQAAgiAEghQAjAcAFA2QABAJgBAKQgcgKgPABIAAAAAhWCJQAbAkAdAiQgbgOgcgNQABgLAAgLQgBgLgBgKIAAAAAiGECQAYAVAaATQgmgLgngKQAPgIAMgLIAAAAAjUC3QAHAJALALQgGAHgHAGQgDgQgCgRIAAAA").cp();
	this.shape_2.setTransform(21.8,43.8);

	this.shape_3 = new cjs.Shape();
	this.shape_3.graphics.f("#CFDD2C").s().p("AD5BMQAug4Asg5QA0ALAuAMQgEADgEAEQhcBahyAoQAFgGAEgHQAKgQAHgSIAAAAAmkhgQgJgNgHgNQAyADA2ADQABACABADQAFAMAFAMQgJgCgKgBQglgkgFALQgCAFAGARQgKgBgLgBQgLgBgLAAIAAAAAlOgQQAHAMAJAKQgLgJgMgKQADgCAEgBIAAAAAkShwQABAFACAGQgEgGgDgGQACAAACABIAAAA").cp();
	this.shape_3.setTransform(50.9,94.7);

	this.shape_4 = new cjs.Shape();
	this.shape_4.graphics.f("#8DC642").s().p("ACdAXIB5h5QCFh8AeAdQAdAciLDEQgbAngcAmQgsA6guA5QATgygKg8QgKgzgcgnIAAAAACbATQgRgUgXgSQgZgVgegMQAhg3Akg1QBSh5ATAKQATAJgvCUQgWBEgZBBIAAAAAA2g2QgwgSg5ACQAQg3AVg2QArh1AZAEQAYAFgLB9QgFA2gIA2IAAAAAkxidQAAgRAFgCQADgBAFAEQANANAZA+QARApAOAqQgDg1AAg3QgBhsAQgCQAQgBAZBZQANAtAKAtQAGg2AKg3QAVhtASgBQASgBAMBrQAFAxACAxQgEAAgFABQjAAOgqDcQgFAcgDAbQg3gigxgwQgRgSgPgSQAJAGAPAMQAHAGAHAGQAMAKALALQgJgMgHgNQgKgPgJgPQgRgegHgRQgGgQACgGQAFgKAlAkQABABACACQAUAUASAXQgLgWgLgWQgFgMgFgMQgBgCgBgDQgdg+AKgJQAKgIAkAvIAAAAQAOARANATQADAGAEAGQgCgGgBgGQgKgfgIgdQgDgKgCgIQgOg5AGgFQAEgDASAYQAKAOAPAYQAVAgASAeQgJgrgHgvQgJg4ACgYIAAAAAhsElQg6gJg0gUQAAgUABgWQAJh/A1gzQAfgfAugLQAugJApAJQBnAaACBwQACBohVAeQggAMgggKQghgKgNgdIANADQARADAOgCQArgJAHg+QAFgogwgRQg0gRglApQgUAWgEAkQgDAlARAgQAIAQALANIAAAA").cp();
	this.shape_4.setTransform(57.2,80);

	this.shape_5 = new cjs.Shape();
	this.shape_5.graphics.f("#3A8E43").s().p("AlmgzQBAhECDg9QC4hYCBAUQCVAYBQCCQAkA8AUBOQglBzhaBdQgugMg0gKQAcgmAbgnQCLjEgdgcQgegdiFB6Ih5B7QgBgCgBgCQAZhDAWhEQAviSgTgJQgTgKhSB5QgkAzgfA3QgDgBgDgBQAIg2ADg0QALh9gWgFQgZgEgtB1QgVA2gQA1QgCAAgCAAQgCgxgFgvQgMhrgSABQgSABgVBtQgKA1gGA2QgKgtgNgrQgZhZgQABQgQACABBsQAAA1ADA1QgOgqgRgpQgZg8gNgNIAAAAAlzgjQgCAYAJA2QAHAvAJAtQgSgggVggQgPgYgKgOQAGgRAJgQQAKgRAQgSIAAAAAmyB0QADgWAFgUQACAIADAKQAIAfAKAfQgCAAgCAAQgNgTgOgTIAAAA").cp();
	this.shape_5.setTransform(63.8,67.8);

	this.shape_6 = new cjs.Shape();
	this.shape_6.graphics.f("#368C43").s().p("AAUgOIgnAdQAEgHADgGIAggQ").cp();
	this.shape_6.setTransform(106.1,51.2);

	this.shape_7 = new cjs.Shape();
	this.shape_7.graphics.f("#9CCA3D").s().p("AgQhvQABABABACQAEAKAEAKQAAgKgBgJQAAgBAAgBQgBgPAAgPQAAgBAAgCQAAgPAAgPQAAgCAAgDQAAgJAAgJQAAgGABgFQAAgEAAgEQABgRABgNQABgGAAgFQACgQACgLQACgIADgFQAEgJAHgBQATgBAWA+IABABQAFAQAGAUQAPAxAKAxQAIg0AMgzQAYhlASABQASABAOBoQAHAzAEA0QAQgzAUgxQAphiAXAJQAWAIgGBlQgDAzgIAxIAyhXQA3hVAXAHQAYAHgYBkQgMAygRAxIBBhMQAagcAUgRQAOAmAJApQgBACgBACQgRAqgVAnQgDAFgEAHIApgdIAPgMQADAeAAAhQAABcgaBSQgUhRgkg8QhQiAiVgXQiBgVi4BYQiDA8hABEQgFgFgDACQgFACAAAQQgQATgKATQgJAPgGARQgSgXgEADQgGAEAOA5QgFAUgDAWQgkgvgKAJQgKAIAdBBQg2gDgygDQggg2gSg9QAGABAFABQAMADALACQAnAKAmALQgagTgYgVQgQgNgPgOQgQgPgNgNQgLgLgHgJQgDgXgBgYQAPgBAcAKQAVAGAdAMQATAIATAJQAcANAbAOQgdgigbgkQgFgGgFgGQgug8gEgXQgCgJAEgEQAPgNBZA4QAgASAgAWQALAIAKAHQgMgSgMgUQgPgXgOgaQgSgigJgXQgPgpAMgLQAUgQBLBKQAJAJAKAKQAGAHAHAHQAQASAQATQADADADADQgCgDgBgEQgGgSgGgRQgDgIgCgIQgGgUgGgUQgDgIgBgHQgHgXgDgSQgKguALgFQAPgIA/BRQAgAoAcAqQgKgzgHg0QgEggAAgWQgBgKABgIQAAgMACgIQADgPAHgCQAOgDAaApQAJAOALAUQAFAIAFAKIAAAAQAIAQAIARQACAEADAEQAHAQAHAQIAAAA").cp();
	this.shape_7.setTransform(55,51.5);

	this.shape_8 = new cjs.Shape();
	this.shape_8.graphics.f("#388F43").s().p("AF1BcQgUARgaAcIhBBMQARgxAMgyQAYhkgYgIQgXgGg3BVIgyBXQAIgxADgzQAGhjgWgIQgXgJgpBgQgUAxgQAzQgEg0gHgzQgOhmgSgBQgQgBgYBjQgMAzgIA0QgKgxgPgxQgGgUgFgQIACACIgDgDQgWg9gTACQgHAAgEAKIiDh+QgFgEAFgEQADgFAGADIDFCoIinjGQgEgGAFgDQAFgFAEAFICzC7IiRjWQgDgGAFgEQAFgDAEAFICeDMIhyjVQAJAAAJABIB4DFIhMjCQAJABAKACIBUCzIgvisQAJADAKADIA5CdIgbiUQAMAEALAFIAhCEIgJh6QAMAFALAHIAPBrIAEhhQAMAGALAIIADBTIAKhLQAMAIAMAKIgEA8IANg1QAMAJALAKIgHApIANgkQAKAKAKAKQABABABABIgHAXIAKgUQALALAKAMIgEAJIAFgIQBBBIAiBWIAAAAAGVDjIgPAMIgiASQAVgoARgqQABgCABgCQAGAbADAdIAAAAAiUApIi3hvQgFgEADgFQADgFAGACICyBeQgBANgBAQIAAAAAjKA2QgLgUgJgOIBKAeQgBAFAAAGQAAAJAAAJIg1gZAiVByIgbgJQgIgRgIgQIAAAAIArAMQAAAPAAAPIAAAAAidCRQgHgQgHgQIAWAEQAAAPABAPIgJgCAiUCVQABAJAAAKQgEgKgEgKIAHABAiLgYQgEAKgCAPIibh3QgFgDADgGQAEgFAFADICaBpAkSATIglgSQgNgKgOgLQgHgGgGgGQAAAAABABIBOAfQgCAHAAAMIAAAAAmNC7IAfgBQACAIADAIIgXgCQgHgGgGgHIAAAAAlgDuIADgBQABADACAEQgDgEgDgCIAAAAAl+CDQABAHADAIIgUgDQgGgBABgGQABgGAGAAIAOAB").cp();
	this.shape_8.setTransform(69.1,25.7);

	this.addChild(this.shape_8,this.shape_7,this.shape_6,this.shape_5,this.shape_4,this.shape_3,this.shape_2,this.shape_1,this.shape);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,110,110);


(lib.title_2_mc = function() {
	this.initialize();

	// Layer 1
	this.shape_9 = new cjs.Shape();
	this.shape_9.graphics.f("#FFFFFF").s().p("ASsjfQAEBVABBKIACBIQAiAGAaAVQAZAVAMAYQANAZAHAYQAHAYABARIACAPIiOATIgBARQgcDSgmA6QgWAOgeAAQgmAAgfgIQgfgIgLgFIgMgFQgBgHgEgKQgEgLgEgOQgDgNgHgzQgGgzgEhTQi/gEhvgnQg8gUAAhWQAAiqCVi4QC3jgCkgKIAHAAQANAKAUARQAVAQAkAtQAkAsAHAhQAFAZAEBXIAAAAANJiHQgcAdAAA4QAAAsAcAUQAcAYApAAQAtAAAmgdQAngaAAgvQAAgqglgdQglgbgtAAQgsAAgcAbIAAAAAIGjcQAWC9AAClQAAEMhCBUQgNARgnAAQhFAAgbgOQhdgoAAlvQAAhQAEhYQAFhZAGgxIAEgwQgBgDgBgEQgCgEgLgLQgKgKgPgJQgPgJgdgKQgegLgmgEQgHgSAAgXQAAgZAOgUQAUgfArgQQAqgQAegCQAfgCA4AAQBhAAAsAPQAIADAFAQQAOA5AVC9IAAAAABgh5QAABggQBuQgPBugRA+IgRA/QgBAHgFAMQgEAMgSAgQgQAfgYAZQgZAZgrATQgrAUg1AAQhGAAg7gdQg6gcgmgsQgmgrgdg/Qgdg+gPg7QgPg5gKhFQgJhBgDgsQgCgtAAgrIACg2QAAgGABgLQACgMAIgeQAJgfANgcQAPgcAegkQAdgiApgYQAngZBAgSQBAgRBOAAQBqAAA8AtQBvBXAAD5IAAAAAlNBoQAAAmAhAfQAhAfAmAAQAoAAAdglQAegkAAgtQAAgngcgdQgbgbgiAAQgqAAgkAjQgkAkAAAqIAAAAArBhSQgwBpguBWQguBWgVAhIgXAhQBogQCiAAIAxAAQgCAoggBVQgfBTgsALQigAnjkAAQgIAAgNgCQgOgDgigJQgjgJgagMQgagOgWgYQgWgaAAghQAAgoAQgmQAOgnA4hlQBIh/A/hUQBAhXAYgbQAYgbAAgNQAAgvisAAQieAAglAPQgYgrAAhRQAAgyAMgTQAkg8C9AAQGoAAAAEDQAABGglBWIAAAA").cp();
	this.shape_9.setTransform(135.8,56.7);

	this.addChild(this.shape_9);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(2.8,6.4,266.1,100.7);


(lib.title_1_mc = function() {
	this.initialize();

	// Layer 1
	this.shape_10 = new cjs.Shape();
	this.shape_10.graphics.f("#FFFFFF").s().p("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").cp();
	this.shape_10.setTransform(239.1,33.9);

	this.addChild(this.shape_10);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(3.6,17.2,471.2,33.4);


(lib.sao_paulo_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance = new lib.SaoPaulo();

	this.addChild(this.instance);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.salvador_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_1 = new lib.Salvador();

	this.addChild(this.instance_1);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.rio_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_2 = new lib.RiodeJaneiro();

	this.addChild(this.instance_2);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.recife_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_3 = new lib.Recife();

	this.addChild(this.instance_3);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.porto_alegre_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_4 = new lib.PortoAlegre();

	this.addChild(this.instance_4);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.natal_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_5 = new lib.Natal();

	this.addChild(this.instance_5);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.manaus_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_6 = new lib.Manaus();
	this.instance_6.setTransform(-120.4,-120.4);

	this.addChild(this.instance_6);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(-120.4,-120.4,241,241);


(lib.hit_mc = function() {
	this.initialize();

	// Layer 1
	this.shape_11 = new cjs.Shape();
	this.shape_11.graphics.f("rgba(223,223,223,0.008)").s().dr(-490,-800,980,1600);
	this.shape_11.setTransform(490,800);

	this.addChild(this.shape_11);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.fortaleza_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_7 = new lib.Fortaleza();

	this.addChild(this.instance_7);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.curitiba_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_8 = new lib.Curitiba();

	this.addChild(this.instance_8);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.cuiaba_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_9 = new lib.Cuiaba();

	this.addChild(this.instance_9);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.braslia_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_10 = new lib.Brasilia();

	this.addChild(this.instance_10);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.bg_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_11 = new lib.ban_do_svd();

	this.addChild(this.instance_11);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.belo_hover_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_12 = new lib.BeloHorizonte();

	this.addChild(this.instance_12);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,241,241);


(lib.thumb_bg_mc = function() {
	this.initialize();

	// Layer 1
	this.instance_13 = new lib.vene_mc();
	this.instance_13.setTransform(154.3,74.8,1,1,0,0,0,149.3,69.7);
	this.instance_13.alpha = 0.5;

	this.shape_12 = new cjs.Shape();
	this.shape_12.graphics.f("#FFC907").s().p("AJYAAQAAD4iwCwQiwCwj4AAQj3AAiwiwQiwiwAAj4QAAj3CwiwQCwiwD3AAQD4AACwCwQCwCwAAD3IAAAA").cp();
	this.shape_12.setTransform(60,60);

	this.addChild(this.shape_12,this.instance_13);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.sao_paulo_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_13 = new cjs.Shape();
	this.shape_13.graphics.f("#FFFFFF").s().p("AHughQAMAOAAAXQAAAYgJAWQgOAigaAAQgDAAgGgBQgRgCgMgHQgMgGgGgGQgGgGgEgJQgEgHAAgFQgBgEAAgEQAAgDAAgEQAAgoALgMQAQgRAnAAQAdAAANAQIAAAAAHTATQgFgHgIAAQgHAAgHAGQgHAHAAAHQAAAGAGAHQAFAGAIAAQAHAAAFgHQAGgHAAgIQAAgGgDgEIAAAAAFLhMQABgCAGgCQAFgDAHAAQATAAAAALIAAABQgDAfAAAZQAABCANACIAHAAQgEATgGAGQgGAGgLAAQgJAAgIgGQgIgFgEgQQgEgPAAgNIADhnQAAgBACgBIAAAAAE9gMQAAAogbA2IgPgEQgKAFgMAAQgTAAgNgLQgNgKgGgPQgFgOAAgSQAAgRAHgSQAHgTAMgLQABgBABAAQADAAAEADQAEADADADIADACQgFAVAAATQAAAcARAAQAKAAAHgMQAIgLADgMQADgOAGgLQAFgMAHAAQAOAAAAAgIAAAAAkUgjQAGAJAFANQAGANACAGQADAIAEAOQAEANADANQADANABAHIABAIQAAAAgCAAQgUAAgRgQQgYARgaAAQgUAAgNgMQgOgMAAgaQAAgQAFgNQAFgMAHgIQAHgJAJgGQAKgHAIgDQAJgEAIgCQAIgDAEAAQAEgBABAAQAHAGAGAKIAAAAAmJhEQgBABgCADQgCADgFADQgEACgHADQgQAFAAAKQAAAKAQAHQAkATAAAaQAAAFAAAFQgEAOgHAKQgJAKgKAFQgLAFgMADQgLACgNAAQgaAAgRgHQADgJAGgFQAFgFAGgCQAGgBAHgEQAIgEAGgHQAFgEAAgGQAAgCgBgBQAAgCgCgBQgBgCgBgBQgBgCgDgBQgCgCgBgBQgCgBgDgBQgDgCgBAAQgBgBgEgBQgDgCgBgBQgMgFgIgLQgJgMAAgPQAAgTAPgMQAPgLATgCQAOgBAIAAQAYAAATAJQgBAEgBACIAAAAAlUAfQAAAGAEAEQAEADAGAAQAHAAAGgEQAGgFAAgHQAAgGgGgEQgFgEgIAAQgGAAgEAEQgEAEAAAJIAAAAAjuAUQAAgoALgMQAQgRAnAAQAdAAAOAQQALAOAAAXQAAAYgJAWQgNAigbAAQgDAAgGgBQgQgCgMgHQgNgGgGgGQgGgGgDgJQgEgHgBgFQAAgEgBgEQAAgDAAgEIAAAAACRgzQAIAGAGAKQAGAJAFANQAFANADAGQADAIAEAOQADANADANQAEANABAHIABAIQgBAAgBAAQgVAAgQgQQgYARgbAAQgUAAgNgMQgNgMAAgaQAAgQAFgNQAEgMAHgIQAIgJAJgGQAJgHAJgDQAIgEAIgCQAJgDADAAQAEgBABAAIAAAAAASgXQAAgHgFgFQgFgEgHAAQgFAAgFAEQgGAFAAAHQAAAJAFAEQAEAFAGAAQAFAAAHgGQAGgFAAgHIAAAAAA0gSQgEANgHAIQgIAKgIAGQgJAFgHADQgIAEgDAAIgGABIABAzQgEABgHAAQgRAAgIgGQgDgUgIguQgHgrAAgPQAAgEABgHQAAgIABgFQABgGABAAQAdgGAYAAQA8AAAAAmQAAAKgEAQIAAAAABiApQAFADAGAAQAHAAAFgEQAGgFAAgHQAAgGgFgEQgGgEgHAAQgGAAgFAEQgEAEAAAJQAAAGAEAEIAAAAAicATQgFgHgIAAQgIAAgHAGQgGAHAAAHQAAAGAFAHQAGAGAHAAQAHAAAGgHQAFgHAAgIQAAgGgCgEIAAAA").cp();
	this.shape_13.setTransform(60.9,61.8);

	// Layer 1
	this.instance_14 = new lib.thumb_bg_mc();
	this.instance_14.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_14,this.shape_13);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.sao_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_15 = new lib.hit_mc();
	this.instance_15.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_16 = new lib.ArenaCorinthians();

	this.addChild(this.instance_16,this.instance_15);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.salvador_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_14 = new cjs.Shape();
	this.shape_14.graphics.f("#FFFFFF").s().p("AHIgDQgDADgBADQgCAEgBAFQgBAFAAAFQAAAHADAUQACAVAAAGQAAADgBACQgFADgUAAQAAAAgBAAQAAAAgCgBQgCAAgCgBQgBAAgCgCQgBgCgBgCQgOg9AAggQAAgSAEgOQAQACALAJQAjgKARAAQAHAAAGABQAAALgEAFQgBAAgDABQgDACgCABQgBABgEACQgEADgCABQgDACgDADQgEADgCADQgCACgDAEIAAAAAF7ghQALAOAAAXQAAAYgJAWQgNAigbAAQgDAAgGgBQgQgCgMgHQgNgGgGgGQgGgGgDgJQgEgHgBgFQAAgEgBgEQAAgDAAgEQAAgoALgMQAQgRAnAAQAdAAAOAQIAAAAAEBgEQAAAJAAAFQAAAOgBANQgCANgDAHQgCAIgDAFQgDAFgBACIgCACQgEAEgIAAQgEAAgDAAQgCAAgKgBQgLgBgIgCQgHgCgLgEQgMgEgHgGQgHgFgFgKQgFgJAAgMQAAgPAGgLQAFgJAIgGQAIgGAKgDQAKgEAIgBQAHgBAIAAIAJABIABgIQACgjADgJQADgDAKAAQAKAAACACQAHAJAEBEIAAAAADbAgQAAgGgFgGQgFgGgHAAQgHAAgGAGQgGAHAAAHQAAAGAEAFQAEAFAIAAQAHAAAGgFQAHgFAAgIIAAAAAFgATQgFgHgIAAQgIAAgHAGQgGAHAAAHQAAAGAFAHQAGAGAHAAQAHAAAGgHQAFgHAAgIQAAgGgCgEIAAAAAjNhQQAGgDAGAAQATAAAAALIAAABQgCAfAAAZQAABCANACIAGAAQgEATgFAGQgGAGgMAAQgJAAgHgGQgJgFgEgQQgEgPAAgNIADhnQABgBABgBQACgCAFgCIAAAAAkGgjQAGAJAFANQAFANADAGQADAIAEAOQADANADANQAEANABAHIABAIQgBAAgBAAQgVAAgQgQQgYARgbAAQgUAAgNgMQgNgMAAgaQAAgQAFgNQAEgMAHgIQAIgJAJgGQAJgHAJgDQAIgEAIgCQAJgDADAAQAEgBABAAQAIAGAGAKIAAAAAmAhEQgBABgCADQgDADgEADQgFACgHADQgPAFAAAKQAAAKAPAHQAlATAAAaQAAAFgBAFQgDAOgIAKQgIAKgLAFQgLAFgLADQgLACgNAAQgbAAgRgHQADgJAGgFQAGgFAFgCQAGgBAIgEQAIgEAFgHQAFgEAAgGQAAgCAAgBQgBgCgBgBQgCgCgBgBQgBgCgCgBQgCgCgCgBQgCgBgCgBQgDgCgCAAQgBgBgDgBQgDgCgBgBQgMgFgJgLQgIgMAAgPQAAgTAPgMQAPgLATgCQANgBAIAAQAZAAATAJQgCAEAAACIAAAAAksAoQAGgFAAgHQAAgGgFgEQgGgEgHAAQgGAAgFAEQgEAEAAAJQAAAGAEAEQAFADAGAAQAHAAAFgEIAAAAABegjQAGAJAFANQAGANACAGQADAIAEAOQAEANADANQADANABAHIABAIQAAAAgCAAQgUAAgRgQQgYARgaAAQgUAAgNgMQgMgMAAgaQAAgQADgNQAFgMAHgIQAHgJAJgGQAKgHAIgDQAJgEAIgCQAIgDAEAAQAEgBABAAQAHAGAGAKIAAAAAA5AoQAGgFAAgHQAAgGgGgEQgFgEgIAAQgGAAgEAEQgEAEAAAJQAAAGAEAEQAEADAGAAQAHAAAGgEIAAAAAgfgJQgLAXgKAdQgKAdgBACQgBADAAAAQgBADgRAAQgJACgIAAQgGAAgBgBQgKgCgGgKQgIgNgHgeQgMgXAFgeQABgCAAgCQABgDABgBIABgBQABAAADgDQADgCAGABQAFAAAGAEQAGAEAHAVQAHATALAiQAAgBAQgoQARgmAEgHQACgCAJAAQAKAAABACQAGAKgLAZIAAAA").cp();
	this.shape_14.setTransform(60,61.8);

	// Layer 1
	this.instance_17 = new lib.thumb_bg_mc();
	this.instance_17.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_17,this.shape_14);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.salvador_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_18 = new lib.hit_mc();
	this.instance_18.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_19 = new lib.FonteNova();

	this.addChild(this.instance_19,this.instance_18);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.rio_normal_mc = function() {
	this.initialize();

	// Layer 4
	this.shape_15 = new cjs.Shape();
	this.shape_15.graphics.f("#FFFFFF").s().p("AHNBFQAdAAAOAQQALAOAAAZQAAAYgJAWQgNAigbAAQgDAAgGgBQgQgCgMgHQgNgGgGgGQgGgGgDgJQgFgHAAgFQAAgEgBgEQAAgDAAgEQAAgqALgMQAQgRAnAAIAAAAAHQCEQgIAAgHAGQgGAHAAAHQAAAGAFAHQAGAGAHAAQAHAAAGgHQAFgHAAgIQAAgGgDgEQgFgHgHAAIAAAAAEXBCQgLABgDAAQgOAAgEgEQgDgDAAgMQAAgGABgBQACgDAGgBQAGgCAFAAQAFAAAEABQADAAADACQAEACABADQACAEAAAHQAAAMgHAAIAAAAAEhBVQACAJAAALQAAAXgDAbQgCAbgDAIQgCAHgDADQgEADgGAAQgHAAgGgCQgGgDgBgCQgFgYAAhKIAAgQIABgBQAAgBABgBQABAAACgBQACAAADAAQANAAAXAHIAAAAADsB9QgtAJAAAUQAAATArAOQgFAGgNAFQgNAFgTAAQgHAAgIgBQgGgBgKgDQgKgEgHgFQgIgGgGgLQgGgLAAgPQgBgGAAgHQAAgjALgNQAIgHAMgFQANgEAIAAQAHgBAHAAQAdAAAOAQQAMAOAAAbIAAAAAGLBHQAAALgEAFQgBAAgDABQgDACgCABQgBABgFACQgDADgCABQgDACgEADQgDADgCADQgDACgCAEQgDAEgCAEQgBAEgBAFQgBAFAAAFQAAAHADAUQACAVAAAGQAAADgCACQgFADgTAAQgBAAAAAAQAAAAgDgBQgCAAgBgBQgCAAgBgCQgBgCgBgCQgOg9AAgiQAAgSAEgOQAQACAKAJQAjgKARAAQAIAAAGABIAAAAAiUjIQAdgGAZAAQA+AAAAAmQAAAKgFAQQgEANgGAKQgJAKgIAGQgBABgCAAQAKALAIAJQAIAKAFAGQAEAGACAEQADAEABACIAAABQgGAFgHADQgIACgJAAQgUgMgQgRQgDgDgCgDIAAAjQgFABgHAAQgRAAgIgGQgCgUgIguQgIgtAAgPQAAgEABgHQABgIABgFQABgGAAAAIAAAAAgTjCQAAgGACgBQABgDAGgBQAHgCADAAQAFAAADABQADAAAEACQADACACADQABAEAAAHQAAAMgHAAQgLABgCAAQgNAAgEgEQgDgDAAgMIAAAAAgTihQABgBABgBQAAAAACgBQADAAACAAQALAAAYAHQABAJAAALQAAAXgDAbQgCAbgDAIQgCAHgDADQgDADgHAAQgFAAgFgCQgHgDgBgCQgFgYAAhKIAAgQIABgBAhXijQgIAAgFAEQgFAFAAAHQAAAJAEAEQAEAFAHAAQAHAAAGgGQAHgFAAgHQAAgHgFgFQgFgEgHAAIAAAAAAhhmQAAgqALgMQARgRAmAAQAeAAANAQQAMAOAAAZQAAAYgKAWQgNAigbAAQgDAAgFgBQgRgCgMgHQgNgGgGgGQgFgGgEgJQgEgHgBgFQAAgEgBgEQAAgDAAgEIAAAAABzhnQgFgHgIAAQgIAAgGAGQgHAHAAAHQAAAGAFAHQAGAGAHAAQAHAAAGgHQAGgHAAgIQAAgGgDgEIAAAAAlNBFQAHgBAHAAQAdAAAOAQQALAOAAAbQgsAJAAAUQAAATArAOQgFAGgOAFQgNAFgSAAQgHAAgIgBQgHgBgKgDQgKgEgHgFQgHgGgGgLQgGgLgBgPQAAgGAAgHQAAgjALgNQAHgHANgFQANgEAIAAIAAAAAmJCCQAAAOgCANQgCANgCAHQgDAIgCAFQgDAFgCACIgCACQgDAEgJAAQgEAAgCAAQgCAAgLgBQgLgBgHgCQgIgCgLgEQgLgEgHgGQgHgFgFgKQgFgJAAgMQAAgPAFgLQAGgLAHgGQAIgGAKgDQALgEAHgBQAIgBAHAAIAJABIABgIQADgjACgJQAEgDAKAAQAJAAADACQAGAJAFBEQAAALAAAFIAAAAAk8BuQAAgHgGgFQgGgFgIAAQgHAAgFAFQgFAFAAAJQAAAHAFAEQAFAEAHAAQAHAAAGgFQAHgEAAgIIAAAAAnTCaQAAAGAEAFQADAFAIAAQAIAAAGgFQAGgFAAgIQAAgGgFgGQgEgGgHAAQgHAAgGAGQgGAHAAAHIAAAAAjkCuIAUAAQAOgCAAhLQAAgbgCgjIAAgBQAAgLAUAAQAIAAAGACQAGADABACQACABABABIADB0QAAAOgFARQgEARgJAGQgJAGgJAAQgNAAgHgHQgGgGgRgVIAAAAABQBLQANAJAIAOQAHAOAAASQACATgGATQgGATgKAMQgCABAAAAQgDAAgFgCQgEgDgDgCIgDgDQADgVgBgSQgDgfgQABQgKABgHANQgGAMgCAOQgDAOgEAMQgEAMgIAAQgMABgCggQgDgqAUg3IAQACQAKgFAMgBQASgBAOAJIAAAAAC4BuQAAgHgGgFQgGgFgIAAQgHAAgEAFQgFAFAAAJQAAAHAFAEQAEAEAHAAQAHAAAHgFQAGgEAAgIIAAAAAiBBmQAHgJAKgGQAJgHAIgDQAJgEAIgCQAIgDAEAAQAEgBABAAQAHAGAHAKQAFAJAFANQAGANACAIQADAIAEAOQAEANADANQADANABAHIABAIQAAAAgCAAQgUAAgRgQQgXARgbAAQgUAAgNgMQgOgMAAgaQAAgQAFgNQAFgOAHgIIAAAAAhwCXQAAAGAEAEQAEADAHAAQAGAAAGgEQAGgFAAgHQAAgGgGgEQgFgEgHAAQgHAAgEAEQgEAEAAAJIAAAA").cp();
	this.shape_15.setTransform(60.9,58.7);

	// Layer 1
	this.instance_20 = new lib.thumb_bg_mc();
	this.instance_20.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_20,this.shape_15);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.rio_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_21 = new lib.hit_mc();
	this.instance_21.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_22 = new lib.Maracana();

	this.addChild(this.instance_22,this.instance_21);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.recife_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_16 = new cjs.Shape();
	this.shape_16.graphics.f("#FFFFFF").s().p("AE8gdQgIAAgGAFQgGAGAAALQAAAHAGAEQAGAFAIAAQAJAAAIgGQAHgDAAgKQAAgIgHgGQgIgFgJAAIAAAAAGEgpQANARAAAeQg0ALAAAZQAAAWAzARQgGAHgQAGQgRAGgUAAQgKAAgIgBQgIgBgNgEQgMgEgJgHQgIgHgHgNQgHgNgBgSQgBgHAAgJQAAgoAOgQQAJgIAPgFQAPgGAKAAQAJgBAHAAQAjAAARATIAAAAADTg3QgRAOgEAWIAsAAQgDAPgNAIQgOAJgOABIAAANQAAAKgBAYQAAAYAAANQgPACgFAAQgPAAgGgGQgFgxAAg6QAAgUACgbQABgRARgLQAQgLAWAAQAQgBALADQAKADAHAHQAGAIACAIQgaADgQANIAAAAAjIgHQAAAHAGAEQAFAFAJAAQAJAAAHgGQAIgDAAgKQAAgIgIgGQgHgFgJAAQgJAAgFAFQgGAGAAALIAAAAAjJg1QAPgGAKAAQAIgBAIAAQAjAAAQATQAOARAAAeQg1ALAAAZQAAAWA0ARQgGAHgQAGQgRAGgUAAQgKAAgJgBQgHgBgNgEQgMgEgJgHQgJgHgGgNQgHgNgCgSQAAgHAAgJQAAgoAOgQQAJgIAPgFIAAAAAkHgWQgEAQgJAKQgJAMgKAHQgCABgCABQAMANAKALQAKALAFAHQAFAHADAGQADAFABACIAAACQgHAGgIACQgKAEgLgCQgXgOgUgUQgDgDgEgEIABAqQgGABgIAAQgUAAgKgIQgDgXgKg3QgJg0AAgSQAAgFACgJQAAgJABgGIACgHQAjgIAeAAQBJAAAAAvQAAAMgFASIAAAAAkvgcQAAgJgGgFQgGgEgJAAQgIAAgHAEQgFAFAAAJQAAALAFAFQAFAFAHAAQAIAAAIgGQAIgGAAgJIAAAAAB+gPQAAAZgDAgQgDAhgEAKQgCAHgEAEQgEAEgIAAQgIAAgHgDQgHgCgBgDQgHgdAAhYIAAgSQAAAAACgBQAAgCABAAQABgBADgBQACAAAEAAQAOAAAdAJQACALAAANIAAAAABvg+QgNABgDAAQgRAAgFgFQgEgEAAgOQAAgHACgBQACgEAIgBQAHgCAGAAQAGAAADAAQAFABADACQAFACACAFQACAEAAAIQAAAOgJABIAAAAAAzgrQAAAJgFALQgNABgJADQgJACgDACIgDACQgTAOAAARQAAAOALANQAMANAPAJQAPAJAPAGQgEAGgOAGQgNAGgSAAQgUgBgSgFQgRgGgKgIQgKgIgGgKQgHgLgBgIQgCgIAAgIQAAgkAWgYQAagcArAAQAXAAARAJQACADAAAGIAAAA").cp();
	this.shape_16.setTransform(59.9,62.6);

	// Layer 1
	this.instance_23 = new lib.thumb_bg_mc();
	this.instance_23.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_23,this.shape_16);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.recife_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_24 = new lib.hit_mc();
	this.instance_24.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_25 = new lib.ArenaPernambuco();

	this.addChild(this.instance_25,this.instance_24);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.porto_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_26 = new lib.hit_mc();
	this.instance_26.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_27 = new lib.EstadioBeiraRio_PortoAlegre();

	this.addChild(this.instance_27,this.instance_26);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.porto_alegre_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_17 = new cjs.Shape();
	this.shape_17.graphics.f("#FFFFFF").s().p("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").cp();
	this.shape_17.setTransform(59.8,63.8);

	// Layer 1
	this.instance_28 = new lib.thumb_bg_mc();
	this.instance_28.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_28,this.shape_17);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.natal_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_18 = new cjs.Shape();
	this.shape_18.graphics.f("#FFFFFF").s().p("AE1gSQAABPAPACIAHABQgEAWgHAIQgHAHgOAAQgKAAgKgHQgKgGgFgTQgFgTAAgPIAEh8QABgBACgCQABgCAHgDQAGgDAJAAQAWAAAAANIAAABQgCAmAAAeIAAAAAD4BiQgYAAgUgTQgdAUggAAQgYAAgQgOQgPgPAAgfQAAgTAFgQQAGgNAJgLQAIgKALgIQAMgIAKgEQAKgEAKgDQAKgDAEgBQAFAAABAAQAJAHAHALQAIAMAGAPQAFAQAEAHQADAKAFAQQAFAQADAQQAEAQABAIIACAJQgBAAgCAAIAAAAAjJhdQAGADAGAEQAGAFACAFQAEAZAAALQACAQAAATQAAALgBAMQgBALgBAJQgCAJgBAFQgBAGgCAEQgCAFAAABQgGASgMANIgFgCQgFgCgLgHQgMgHgMgLQgHgFgGgIQgHgIgEgGIgDgFQABARAAAPQAAAYgDABQgMAFgOAAIgJgBQgRg9AAgwQAAgMABgOQABgNACgIQABgIABAAIA2ATQAJAEALAFQAMAFAGADIAGAEIgChKQACAAACAAQADABAHABQAHACAGACIAAAAAA9hWQAAAMgBALQALAAANgBIANgBIABAAQAAAAABABQAAAAABABQABABAAACQABACAAADQAAALgGAYQgHABgKAAQgKABgKgBIAAABQAABPAPACIAHABQgDAWgHAIQgIAHgOAAQgKAAgJgHQgKgGgFgTQgFgTAAgPIABg5QgDAAgCgBQgFgCgBgDQgCgDgBgGQAAgHABgFQACgGABgBQAEgBAHgBIABgfQABgBACgCQABgCAHgDQAHgDAIAAQAWAAAAANIAAABACnAiQAAgIgGgFQgHgFgIAAQgIAAgFAFQgFAFAAALQAAAHAFAFQAFAEAIAAQAIAAAHgFQAGgGAAgIIAAAAAg7g9QAJAHAIALQAHAMAGAPQAGAQADAHQADAKAGAQQAEAQAEAQQADAQAAAIIABAJQAAAAgBAAQgYAAgUgTQgcAUghAAQgXAAgQgOQgQgPAAgfQAAgTAFgQQAGgNAJgLQAIgKALgIQAMgIAKgEQAKgEAKgDQAKgDAEgBQAFAAABAAIAAAAAhmA1QAIAAAHgFQAGgGAAgIQAAgIgGgFQgHgFgIAAQgIAAgFAFQgFAFAAALQAAAHAFAFQAFAEAIAAIAAAA").cp();
	this.shape_18.setTransform(60.4,62.7);

	// Layer 1
	this.instance_29 = new lib.thumb_bg_mc();
	this.instance_29.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_29,this.shape_18);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.natal_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_30 = new lib.hit_mc();
	this.instance_30.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_31 = new lib.ArenadasDunas();

	this.addChild(this.instance_31,this.instance_30);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.manaus_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_19 = new cjs.Shape();
	this.shape_19.graphics.f("#FFFFFF").s().p("AHTg4QAOAGACAMQgMABgUAFQgUAFAAAIQAAADACADQAGAHALAGQAMAHAIAGQAKAFAHAKQAHAJAAALQgCAVgbAOQgbAPgeAAQgUAAgQgIQgRgHAAgIQAAgDAFAAQARAAAPgHQAPgIAAgLQAAgHgHgHQgFgFgIgHQgJgHgFgHQgGgGgFgIQgEgHAAgHQAAgSAYgLQAXgMAcAAQAUAAAOAHIAAAAAFZgSQAAAxghBAIgTgFQgLAHgPAAQgWAAgQgNQgQgMgGgSQgHgRAAgVQAAgWAIgWQAJgWAPgOQABgBACAAQACAAAFAEQAFADAEAEIAEADQgHAYAAAXQAAAiAUAAQAMAAAKgNQAIgOAEgPQAEgQAHgOQAHgOAIAAQARAAAAAmIAAAAAjHhAQAFAAABAAQAJAHAHALQAIAMAGAPQAGAQADAIQADAKAGAPQAEAQAEAQQADAQACAJIABAIQgBAAgCAAQgYAAgUgTQgdAVggAAQgXAAgQgPQgQgPAAgeQAAgUAFgQQAGgNAJgLQAIgKALgIQAMgIAKgEQAKgEAKgDQAKgDAEgBIAAAAAlVhhQABAAAAAAQAfAUAABNQAAAjgHAvQgCAOgSABQgGAAgIgCQgHgCgBgDQgBgeAAgbQAAgOAAgGQgKAfgLAUQgKATgFAFIgFAFQgBgBgCgBQgBgBgFgGQgFgGgFgGQgEgHgGgMQgFgLgEgOIAAAUQAAAigHAMQgBADgNAAQgNAAgCgDQgHgPgFgoQgFgngBggIgBggQAMgEAaAAQANAAAIAEQAJAEAFAFQAGAGAJAMQABgBACgDQADgEAHgIQAHgJAHgIQAHgHALgIQAKgIAJgEIAAAAAjdAtQAGgFAAgJQAAgIgGgFQgHgFgIAAQgIAAgFAFQgFAFAAALQAAAIAFAEQAFAEAIAAQAIAAAHgFIAAAAACUguQAIAMAGAPQAGAQAEAIQADAKAFAPQAEAQAEAQQAEAQABAJIABAIQgBAAgBAAQgZAAgUgTQgdAVgfAAQgYAAgQgPQgQgPAAgeQAAgUAFgQQAGgNAJgLQAJgKAKgIQAMgIAKgEQAKgEAKgDQAKgDAFgBQAFAAAAAAQAJAHAHALIAAAAABoAtQAGgFAAgJQAAgIgGgFQgHgFgIAAQgHAAgFAFQgGAFAAALQAAAIAGAEQAFAEAHAAQAIAAAHgFIAAAAAAMA3QgHAWgMAPQgBABgBAAQgDAAgFgDQgFgDgEgCIgEgDQAEgZgCgXQgDgjgUABQgMAAgHAPQgIAPgDAQQgCAQgGAPQgFAPgIAAQgRABgDgmQgEgyAbhBIATADQALgHAPgBQAWgBARAKQAPALAHASQAIARACASQACAYgHAXIAAAA").cp();
	this.shape_19.setTransform(60.2,63);

	// Layer 1
	this.instance_32 = new lib.thumb_bg_mc();
	this.instance_32.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_32,this.shape_19);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.manaus_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_33 = new lib.hit_mc();
	this.instance_33.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_34 = new lib.ArenaAmazonia();

	this.addChild(this.instance_34,this.instance_33);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.fortaleza_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_20 = new cjs.Shape();
	this.shape_20.graphics.f("#FFFFFF").s().p("AHQgjQAGAJAFANQAGANACAGQADAIAEAOQAEANADANQADANABAHIABAIQAAAAgCAAQgUAAgRgQQgYARgaAAQgUAAgNgMQgOgMAAgaQAAgQAFgNQAFgMAHgIQAHgJAJgGQAKgHAIgDQAJgEAIgCQAIgDAEAAQAEgBABAAQAHAGAGAKIAAAAAGUASQgEAEAAAJQAAAGAEAEQAEADAGAAQAHAAAGgEQAGgFAAgHQAAgGgGgEQgFgEgIAAQgGAAgEAEIAAAAAFUgFQgDADgEADQgEAEgDAGQgEAFgDAHQgCAHAAAFQAAAHADAFQAEAGAGACQAGACAFABQAGACAFAAQABAAACgBQACAAACAAQADAAAAACQAAAKgQAGQgQAGgRAAQgOAAgLgBQgLgCgHgCQgHgCgFgEQgFgEgCgDQgCgDgCgEQgBgDAAgCQAAgCAAgCQAAgBAAAAQAAgIAFgIQAFgIAHgHQAHgGAHgGQAIgFAEgEQAFgGAAgDQAAgLghAAQgNAAgEABQAAgBACgCQABgDAEgEQAEgEAFgEQAFgEAHgDQAIgCAJAAQAIAAAJABQAIACAKADQAKAEAGAGQAHAGAAAJQAAAGgEAEIgDAEADXBOQgNAFgSAAQgHAAgIgBQgHgBgKgDQgKgEgHgFQgHgGgGgLQgGgLgBgPQAAgGAAgHQAAghALgNQAHgHANgFQANgEAIAAQAHgBAHAAQAdAAAOAQQALAOAAAZQgsAJAAAUQAAATArAOQgFAGgOAFIAAAAAjmgmQAjgKARAAQAIAAAGABQAAAAAAABQADgBAGgBIABgaQABgBABgBQACgCAFgCQAGgDAGAAQATAAAAALIAAABQgBAKAAAJQAJAAALgBIALgBIAAAAQABAAAAABQABAAAAABQABABAAABQABACAAACQAAAKgFAUQgGAAgIABQgIAAgJAAIAAAAQAABCANACIAGAAQgEATgFAGQgGAGgMAAQgJAAgHgGQgJgFgEgQQgEgPAAgNIACgvQgDAAgCgBQgEgBgDgDQgCgCAAgFQAAgBAAAAQgBABgCAAQgEACgBABQgCABgEACQgDADgDABQgCACgEADQgDADgCADQgDACgCAEQgDADgCADQgBAEgBAFQgBAFAAAFQAAAHACAUQADAVAAAGQAAADgCACQgFADgTAAQgBAAAAAAQgBAAgCgBQgCAAgBgBQgCAAgBgCQgCgCAAgCQgOg9AAggQAAgSADgOQAQACALAJIAAAAAmOgzQgMACgJAEQgKAFgFAEQgFAEgDADQgEAEgBACIAAACIAjABQAAALgDAIQgEAGgEADQgEAEgHACQgGACgDAAQgCAAgEAAQAAAWAAARQABAQABAFIAAAFQgPACgKAAQgJAAgGgBQgGgBgBgCIgCgBQgBgGgBgNQgBgOgBgQQgBgQAAgWQAAgfAEgkQAPgHAjAAQARAAAKACQAJADAHAIQAGAIAAALIAAAAAlFgxQAdAAANAQQALAOAAAXQAAAYgIAWQgOAigaAAQgDAAgGgBQgRgCgMgHQgMgGgHgGQgFgGgEgJQgEgHAAgFQgBgEgBgEQAAgDAAgEQAAgoAMgMQAQgRAnAAIAAAAAlDAMQgHAAgHAGQgHAHAAAHQAAAGAGAHQAFAGAIAAQAHAAAFgHQAGgHAAgIQAAgGgDgEQgFgHgIAAIAAAAACTgGQAAAGAFADQAFAEAHAAQAHAAAGgFQAHgCAAgIQAAgHgGgFQgGgFgIAAQgHAAgFAFQgFAFAAAJIAAAAABagPQAABCANACIAHAAQgEATgGAGQgGAGgLAAQgJAAgIgGQgIgFgEgQQgEgPAAgNIADhnQAAgBACgBQABgCAGgCQAFgDAHAAQATAAAAALIAAABQgDAfAAAZIAAAAAAPgNQAGANACAGQADAIAEAOQAEANADANQADANABAHIABAIQAAAAgCAAQgUAAgRgQQgWARgaAAQgUAAgNgMQgOgMAAgaQAAgQAFgNQAFgMAHgIQAHgJAJgGQAKgHAIgDQAJgEAIgCQAIgDAEAAQAEgBABAAQAHAGAEAKQAGAJAFANIAAAAAgsAOQgGAAgEAEQgEAEAAAJQAAAGAEAEQAEADAGAAQAHAAAGgEQAGgFAAgHQAAgGgGgEQgFgEgIAAIAAAA").cp();
	this.shape_20.setTransform(60.9,61.8);

	// Layer 1
	this.instance_35 = new lib.thumb_bg_mc();
	this.instance_35.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_35,this.shape_20);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.fortaleza_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_36 = new lib.hit_mc();
	this.instance_36.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_37 = new lib.EstadioCastelao();

	this.addChild(this.instance_37,this.instance_36);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.curitiba_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_21 = new cjs.Shape();
	this.shape_21.graphics.f("#FFFFFF").s().p("AHJgqQAHAMAGAPQAGAPAEAIQADAKAFAQQAEAQAEAQQAEAQABAIIABAJQAAAAgCAAQgZAAgUgTQgcAUggAAQgYAAgQgOQgQgPAAgfQAAgTAGgQQAFgNAJgLQAJgKAKgIQAMgIAKgEQAKgEAKgDQAKgDAFgBQAFAAAAAAQAJAHAIALIAAAAAGiAjQAAgIgGgFQgHgFgIAAQgHAAgFAFQgGAFAAALQAAAHAGAFQAFAEAHAAQAIAAAHgFQAGgGAAgIIAAAAAEtgQQAJAHAHALQAGAOAAARQAAAPgFALQgHALgIAHQgJAHgOAFQgNAFgJACQgJACgNABQgOABgCAAQgDAAgFAAQgKAAgEgEIgCgCQgDgDgDgGQgDgHgDgJQgDgJgCgPQgCgPAAgRQAAgGAAgLQAFhSAIgKQADgDAMAAQAMAAADADQAEAMADApIABAKIAMgBQAIAAAJABQAJABANAEQANAFAJAHIAAAAADmAZQgHAHAAAIQAAAJAIAGQAHAGAKAAQAJAAAFgGQAEgHAAgGQAAgJgHgHQgHgIgJAAQgIAAgFAHIAAAAACXhgQAEACACAFQADAFAAAHQAAAPgJAAQgOABgCAAQgMAAgGgCQAAAAAAABQABABAAACQACACAAADQAAADgBADQABAAAAAAQAPAAAcAJQACALAAANQAAAZgDAhQgCAhgEAJQgDAIgDADQgFAEgIAAQgHAAgIgCQgHgDgBgDQgGgcAAhTQgFABgGAAQgKABgKgBIAAABQAABPAPACIAHABQgEAWgHAIQgHAHgOAAQgKAAgKgHQgKgGgFgTQgFgTAAgPIACg5QgEAAgBgBQgGgCgDgDQgCgDgBgGQAAgHACgFQACgGACgBQAEgBAIgBIABgfQAAgBACgCQACgCAHgDQAGgDAJAAQAWAAAAANIAAABQgBAMgBALQAMAAANgBIANgBQgBAAgBgBQgFgEAAgPQAAgGACgCQADgDAHgCQAHgCAGAAQAHAAADABQAEAAAEACIAAAAAgChgQACACACAFQADAFAAAHQAAAPgHAAQgOABgDAAQgRAAgEgEQgFgEAAgPQAAgGACgCQADgDAHgCQAHgCAGAAQAGAAAEABQAEAAAEACIAAAAAlUhhQAEABAAAKQAAAIgEAJQgDAJgFABQgWAFgTAKQgUAJgOAPQgOAPAAAOQAAAMAEAJQAEAKAHAFQAHAGAJAEQAIAEAKACQAKACAIABQAIABAJAAQgGAUgSAJQgQAIgcAAQgUAAgLgEQgQgGgLgNQgLgNgEgQQgEgQgCgLQgBgLAAgKQAAgaALgXQAKgWAUgOQAbgSAvAAQAXAAAXAEIAAAAAkSgvQgGAYAAAXQAAAiAUAAQAMAAAJgNQAJgOAEgPQAEgQAHgOQAGgOAIAAQARAAAAAmQAAAxggBAIgTgFQgLAGgPAAQgXAAgPgMQgQgMgHgSQgGgSAAgUQAAgWAIgWQAJgWAPgOQABgBABAAQADAAAFAEQAFADAEAEIADADAAKgPQAAAZgDAhQgCAhgEAJQgBAIgEADQgEAEgIAAQgIAAgHgCQgHgDgBgDQgGgdAAhXIAAgSQAAgBABgBQAAgBABgBQABgBADAAQACgBAEAAQAOAAAbAJQACALAAANIAAAAAg8glQAAAAgEACQgDACgCABQgDABgEADQgEADgDACQgDACgEAEQgFADgCADQgDAEgDAEQgDADgCAFQgCAFgCAFQgBAGAAAHQAAAHADAZQADAZAAAHQAAAEgBACQgGAEgYAAQAAAAgBAAQgBAAgCgBQgCAAgCgBQgCgBgCgCQgCgCAAgDQgRhJAAgmQAAgXAEgQQATACANALQArgMAUAAQAJAAAHABQAAANgGAGIAAAA").cp();
	this.shape_21.setTransform(60.2,59.1);

	// Layer 1
	this.instance_38 = new lib.thumb_bg_mc();
	this.instance_38.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_38,this.shape_21);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.curitiba_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_39 = new lib.hit_mc();
	this.instance_39.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_40 = new lib.ArenadaBaixda();

	this.addChild(this.instance_40,this.instance_39);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.cuiaba_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_22 = new cjs.Shape();
	this.shape_22.graphics.f("#FFFFFF").s().p("AG+BjQgZAAgTgTQgdAUggAAQgYAAgQgOQgQgPAAgfQAAgTAGgQQAGgNAIgLQAJgKALgIQALgIAKgEQALgEAJgDQAKgDAFgBQAFAAABAAQAIAHAIALQAHAMAGAPQAGAPAEAIQADAKAFAQQAEAQAEAQQAEAQABAIIABAJQAAAAgCAAIAAAAAEHACQAHAOAAARQAAAPgGALQgHALgIAHQgJAHgNAFQgOAFgJACQgJACgNABQgNABgDAAQgCAAgFAAQgLAAgEgEIgCgCQgCgDgEgGQgDgHgDgJQgDgJgCgPQgCgPAAgRQAAgGAAgLQAFhSAJgKQACgDAMAAQAMAAAEADQADAMADApIABAKIAMgBQAIAAAJABQAJABANAEQANAFAJAHQAKAHAGALIAAAAADUAqQAAgJgHgHQgHgIgJAAQgHAAgGAHQgGAHAAAIQAAAJAHAGQAIAGAJAAQAJAAAFgGQAEgHAAgGIAAAAAFLAyQAFAEAHAAQAIAAAHgFQAHgGAAgIQAAgIgHgFQgGgFgJAAQgHAAgFAFQgGAFAAALQAAAHAGAFIAAAAAjvg9QADAAAFAEQAFADADAEIAEADQgGAYAAAXQAAAiAUAAQAMAAAJgNQAIgOAEgPQAEgQAHgOQAHgOAIAAQARAAAAAmQAAAxghBAIgSgFQgMAGgPAAQgWAAgQgMQgPgMgIgSQgGgSAAgUQAAgWAJgWQAIgWAPgOQABgBACAAIAAAAAkahWQAAAIgDAJQgEAJgFABQgVAFgUAKQgUAJgOAPQgOAPAAAOQAAAMAEAJQAFAKAHAFQAHAGAIAEQAIAEAKACQAKACAJABQAHABAJAAQgFAUgSAJQgRAIgcAAQgUAAgKgEQgRgGgKgNQgLgNgEgQQgFgQgBgLQgCgLAAgKQAAgaALgXQALgWAUgOQAagSAvAAQAYAAAWAEQAEABAAAKIAAAAABKgqQAIAMAGAPQAFAPAEAIQADAKAGAQQAEAQADAQQAEAQABAIIACAJQgBAAgCAAQgYAAgUgTQgdAUggAAQgVAAgQgOQgQgPAAgfQAAgTAFgQQAGgNAJgLQAIgKALgIQAKgIAKgEQAKgEAKgDQAKgDAEgBQAFAAABAAQAJAHAHALIAAAAAAeAWQgHgFgIAAQgIAAgFAFQgDAFAAALQAAAHADAFQAFAEAIAAQAIAAAHgFQAGgGAAgIQAAgIgGgFIAAAAAhOhiQAEAAAEACQAFACACAFQACAFAAAHQAAAPgJAAQgNABgDAAQgRAAgFgEQgEgEAAgPQAAgGABgCQADgDAHgCQAIgCAGAAQAGAAADABIAAAAAg4gPQAAAZgDAhQgCAhgEAJQgCAIgEADQgEAEgIAAQgIAAgHgCQgIgDgBgDQgGgdAAhXIAAgSQAAgBABgBQABgBABgBQABgBACAAQADgBADAAQAPAAAdAJQABALAAANIAAAA").cp();
	this.shape_22.setTransform(60.2,60.1);

	// Layer 1
	this.instance_41 = new lib.thumb_bg_mc();
	this.instance_41.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_41,this.shape_22);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.cuiaba_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_42 = new lib.hit_mc();
	this.instance_42.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_43 = new lib.Pantanal();

	this.addChild(this.instance_43,this.instance_42);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.brasilia_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_44 = new lib.hit_mc();
	this.instance_44.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_45 = new lib.ManeGarrincha();

	this.addChild(this.instance_45,this.instance_44);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);


(lib.brasila_normal_mc = function() {
	this.initialize();

	// Layer 2
	this.shape_23 = new cjs.Shape();
	this.shape_23.graphics.f("#FFFFFF").s().p("AGiBOQgdAVggAAQgXAAgQgOQgQgPAAgfQAAgTAFgQQAGgNAJgLQAIgKALgIQAMgIAKgEQAKgEAKgDQAKgDAEgBQAFAAABAAQAJAHAHALQAIAMAGAPQAGAQADAHQADAKAGAQQAEAPAEARQADAQACAIIABAJQgBAAgCAAQgYAAgUgUIAAAAAD9hkQAXAAAAANIAAABQgDAmAAAeQAABPAPACIAIAAQgEAXgHAHQgHAIgOAAQgLAAgJgHQgKgGgFgUQgFgSAAgPIAEh8QAAgBACgCQACgCAGgDQAHgDAIAAIAAAAAF3AVQgHgFgIAAQgIAAgFAFQgFAFAAALQAAAHAFAEQAFAFAIAAQAIAAAHgFQAGgGAAgIQAAgIgGgFIAAAAAj6guQArgMAUAAQAJAAAHABQAAANgGAGQAAAAgEACQgDACgCABQgDABgEADQgEADgDACQgDACgEAEQgFADgCADQgDAEgDAEQgDAEgCAEQgCAFgCAFQgBAGAAAHQAAAHADAZQADAZAAAHQAAAEgBACQgGAEgYAAQAAAAgBAAQgBAAgCgBQgCAAgCgCQgCAAgCgCQgCgDAAgCQgRhJAAgmQAAgXAEgQQATACANALIAAAAAmAgvQgHgFgJAAQgJAAgFAFQgGAFAAAKQAAAIAFAFQAFAFAJAAQAJAAAHgFQAHgFAAgKQAAgIgGgFIAAAAAlVgNQArAUAAAhQAAAOgIAMQgIALgMAGQgLAHgPAEQgOAEgMACQgLABgJAAQgTAAgOgKQgOgKgGgQQgHgRgDgPQgDgPAAgQQAAgTAHgZQAIgZAHgOIAHgOQAFABAHABQAHAAATAFQASAEAOAGQAPAFALALQAMALAAAOQAAAMgOAMIAAAAAmGAoQAAAJAFAFQAFAFAJAAQAJAAAHgGQAHgFAAgKQAAgHgHgGQgHgFgJAAQgIAAgFAFQgGAFAAAKIAAAAACvhkQAGAAAEABQAEAAAEACQAEACACAFQACAFAAAHQAAAPgIAAQgOABgDAAQgRAAgEgEQgFgEAAgPQAAgGACgCQADgDAHgCQAHgCAGAAIAAAAACcgwQACgBAEAAQAOAAAdAJQACALAAANQAAAZgDAhQgDAhgDAJQgDAIgEADQgEAEgIAAQgIAAgHgCQgHgDgBgDQgGgdAAhXIAAgSQAAgBABgBQAAgBABgBQABgBADAAIAAAAAB/gjQgMABgUAFQgUAFAAAIQAAADACADQAGAHALAGQALAHAJAGQAJAFAIAKQAHAJAAALQgCAVgbAOQgbAPgeAAQgVAAgQgIQgPgHAAgIQAAgDAEAAQARAAAPgIQAPgHAAgMQAAgGgHgHQgFgGgJgHQgIgGgFgHQgGgGgGgIQgEgHAAgHQAAgSAYgLQAYgMAcAAQAUAAAOAHQAOAGACAMIAAAAAhAg9QAJAHAHALQAIAMAGAPQAGAQADAHQADAKAGAQQAEAPAEARQADAQACAIIABAJQgBAAgCAAQgYAAgUgUQgdAVggAAQgXAAgQgOQgQgPAAgfQAAgTAFgQQAGgNAJgLQAIgKALgIQAMgIAKgEQAKgEAKgDQAKgDAEgBQAFAAABAAIAAAAAhrA1QAIAAAHgFQAGgGAAgIQAAgIgGgFQgHgFgIAAQgIAAgFAFQgFAFAAALQAAAHAFAEQAFAFAIAAIAAAA").cp();
	this.shape_23.setTransform(60.2,60.2);

	// Layer 1
	this.instance_46 = new lib.thumb_bg_mc();
	this.instance_46.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_46,this.shape_23);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.belo_normal_mc = function() {
	this.initialize();

	// Layer 5
	this.shape_24 = new cjs.Shape();
	this.shape_24.graphics.f("#FFFFFF").s().p("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gBAKgQAGQgQAGgOAAQgPAAgLgBQgLgCgHgCQgHgCgFgEQgFgEgCgDQgCgDgBgEQgCgDAAgCQAAgCAAgCQAAgBAAAAQAAgIAFgIQAFgIAHgHQAIgGAHgGQAHgGAFgFQAFgGAAgDQAAgLgiAAQgNAAgDABQAAgBABgCQACgDADgEQAEgEAFgEQAFgEAIgDQAIgCAIAAQAIAAAJABQAJACAIADQAKAEAGAGQAHAGAAAJQAAAGgEAEIgDAEQgDADgEAFQgEAEgEAGIAAAAABsB8QgHAAgHAGQgHAHAAAHQAAAGAGAHQAFAGAIAAQAHAAAFgHQAGgHAAgIQAAgGgDgEQgFgHgIAAIAAAAAiXheQgGAAgFAFQgFAEAAAJQAAAHAEAEQAEAEAIAAQAHAAAGgEQAGgFAAgIQAAgGgGgFQgGgFgHAAIAAAAAh/AhQACgDAGgBQAGgCAFAAQAFAAADABQADAAAEACQADACACADQACAEAAAHQAAAMgIAAQgLABgCAAQgOAAgEgEQgEgDAAgMQAAgGACgBIAAAAAh8BGQACAAADAAQAMAAAYAHQABAJAAALQAAAXgCAbQgCAbgDAIQgCAHgEADQgDADgHAAQgGAAgGgCQgGgDgBgCQgFgYAAhKIAAgQIABgBQAAgBABgBQABAAACgBIAAAA").cp();
	this.shape_24.setTransform(61.2,60.6);

	// Layer 1
	this.instance_47 = new lib.thumb_bg_mc();
	this.instance_47.setTransform(60,60,1,1,0,0,0,60,60);

	this.addChild(this.instance_47,this.shape_24);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,120,120);


(lib.belo_detail_mc = function() {
	this.initialize();

	// hit
	this.instance_48 = new lib.hit_mc();
	this.instance_48.setTransform(490,800,1,1,0,0,0,490,800);

	// Layer 1
	this.instance_49 = new lib.Mineirao();

	this.addChild(this.instance_49,this.instance_48);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(0,0,980,1600);

})(lib = lib||{}, images = images||{}, createjs = createjs||{});
var lib, images, createjs;