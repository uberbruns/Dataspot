var App = Ember.Application.create({


	ready : function() {

		// System
		_.mixin(_.string.exports());

		// Update View
		App.adjustDocumentScrollingBehavior()
		App.libraryController.update()
		
	},


	adjustDocumentScrollingBehavior: function() {

		document.ontouchmove = function(e) {
			e.preventDefault()
		};

	},


	selected: function(key) {

		var libController = App.libraryController

		if (key == 'libraryController') {
			return libController;
		} else if (key == 'library') {
			return libController.get('selectedLibrary')
		} else if (key == 'indexController') {
			return libController.get('selectedLibrary.indexController')
		} else if (key == 'propertiesController') {
			return libController.get('selectedLibrary.propertiesController')
		} else if (key == 'sectionsController') {
			return libController.get('selectedLibrary.sectionsController')
		} else if (key == 'section') {
			return libController.get('selectedLibrary.sectionsController.selectedSection')
		} else if (key == 'indexCard') {
			return libController.get('selectedLibrary.indexController.selectedIndexCard')
		} else if (key == 'recordController') {
			return libController.get('selectedLibrary.indexController.selectedIndexCard.recordController')
		} else if (key == 'record') {
			return libController.get('selectedLibrary.indexController.selectedIndexCard.recordController.selectedRecord')
		} else if (key == 'field') {
			return libController.get('selectedLibrary.indexController.selectedIndexCard.recordController.selectedField')
		}

	},


	panelDidInsert: function(scope) {

		scope.$('.scrollarea').each(function() {
			this.ontouchmove = function(e) {
				e.stopPropagation();
			};
		})

		scope.$('.menu').click(function() {
			if (!this.focused) this.focus()
			else this.blur()
		}).focus(function() {
			$(this.parentNode).children('.menu-content').addClass('menu-is-visible')
		}).blur(function() {
			var menuContentElement = $(this.parentNode).children('.menu-content');
			setTimeout(function() {
				menuContentElement.removeClass('menu-is-visible')
			}, 200)
		})

	},

})



App.defaults = Ember.Object.create({

	apiProtocol : window.document.location.protocol,
	apiHostname : window.location.hostname,
	apiPort : window.location.port,
	apiName : window.location.pathname + 'api',

	baseResourceUrl : function() {

		var url = this.get('apiProtocol') + '//' + this.get('apiHostname') + ':' + this.get('apiPort') + this.get('apiName')
		console.log(url)

		return url;

	}.property(),

})




