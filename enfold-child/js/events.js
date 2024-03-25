(function(a, b){

	var key;

	return a[a.eventsObj] = {
	    init: function (key) {
	    	this.key = key;
	    },
	    getKey: function() {
	    	return this.key;
	    },
	    call: function(name, args) {
	    	if (typeof this[name] !== "function") {
	    		throw "Invalid function name.";
	    	}
	    	this[name].call(null, args);
	    },
	    fbPixel: function() {
	    	
	    	a[a.eventsObj].loadFile('https://www.alliancebhc.org/wp-content/themes/enfold-child/js/fb-pixel-partial.js', 'script');
	    	a[a.eventsObj].loadFile('https://www.alliancebhc.org/wp-content/themes/enfold-child/js/fb-pixel-no-script.html', 'noscript');
	    
	    },
	    getSealImage: function(element) {

			if (element.charAt(0) == '#') {
				element = element.substr(1);
			}
	   
	    	if (!document.getElementById(element)) {
	    		throw "Element ID not found.";
	    	}

	    	var img = document.createElement("img");
			img.setAttribute('src', 'https://www.alliancebhc.org/get-seal-image?id=' + a[a.eventsObj].getKey() + '&side=1');
			img.setAttribute('onmouseover', "this.src='https://www.alliancebhc.org/get-seal-image?id=" + a[a.eventsObj].getKey() + "&side=2'");
			img.setAttribute('onmouseout', "this.src='https://www.alliancebhc.org/get-seal-image?id=" + a[a.eventsObj].getKey() + "&side=1'");
			img.setAttribute('id', "img-alliance-partner");
			img.setAttribute('width', "150");
			img.setAttribute('width', "150");
			document.getElementById(element).appendChild(img); 
	
	    },
	    loadFile: function(url, element) {

	    	var xhr = new XMLHttpRequest();
			xhr.open('GET', url);
			xhr.onload = function() {
			    if (xhr.status === 200) {
			        return a[a.eventsObj].addContentToDom(xhr.responseText, element);
			    }
			    else {
			        return;
			    }
			};
			xhr.send();
	    },
	    addContentToDom(content, element) {
	    	var domElement = document.createElement(element);
			domElement.type="text/javascript";
			domElement.innerHTML = content;
			document.getElementsByTagName('head')[0].appendChild(domElement);
	    }
	}

})(window, document);