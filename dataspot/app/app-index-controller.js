
App.CAIndexCard = Ember.Resource.extend({

	resourceUrl: undefined,
	resourceName: 'indexCard',
	resourceProperties: ['id','libraryId', 'labels'],
	resourceIdField: 'id',
	recordController: null,
	isSelected: false,

	id: undefined,
	libraryId: undefined,
	labels: Ember.A([]),

	init: function() {

		this.set('recordController', App.RecordController.create({ indexCard: this }));
	 
	},

	attributedLabels: function() {

		var propertiesController = this.get('controller.library.propertiesController')
		var labelFieldProperties = propertiesController.get('labelFieldProperties')
		var attributedLabels = Ember.A([])

		this.get('labels').forEach(function(label) {

			var labelFieldProperties = propertiesController.propertiesWithId(label['id'])
			var attributedLabel = Ember.Object.create({})
			attributedLabel.set('style', labelFieldProperties.get('label'))
			attributedLabel.set('insert', labelFieldProperties.get('insert'))
			attributedLabel.set('prepend', labelFieldProperties.get('prepend'))
			attributedLabel.set('append', labelFieldProperties.get('append'))
			attributedLabel.set('value', label['value'])
			attributedLabels.push(attributedLabel);

		})

		return attributedLabels

	}.property('labels','controller'),


})





App.IndexCardController = Ember.ResourceController.extend({

	resourceType: App.CAIndexCard,
	selectedIndexCard: undefined,
	library: null,


	_resourceUrl: function() {

		return this.get('library')._resourceUrl() + '/records';

	},


	selectedIndexCardChanged: Ember.observer(function() {

		var self = this;
		var selectedIndexCard = this.get('selectedIndexCard')

		if (selectedIndexCard && selectedIndexCard.get('recordController')) {
		
			selectedIndexCard.get('recordController').update(function() {

				self.get('indexCards').forEach(function(anIndexCard)  {

					anIndexCard.set('isSelected', (anIndexCard == selectedIndexCard))

				})

			})

		}
		
	 }, 'selectedIndexCard'),



	findSelectedIndexCard: function() {

		if (this.get('indexCards') && this.get('indexCards').get('length') > 0) {

			var thisSelectedIndexCard = this.get('selectedIndexCard')
			var newSelectedIndexCard = this.get('indexCards')[0];
			var thisSelIndexCardTime = (thisSelectedIndexCard) ? thisSelectedIndexCard.get('timeCreated') : Date.now()

			this.get('indexCards').forEach(function(anIndexCard) {

				if (thisSelectedIndexCard && anIndexCard.get('id') == thisSelectedIndexCard.get('id')) {
					newSelectedIndexCard = anIndexCard;
				}

				var anIndexCardTime = anIndexCard.get('time_created')
				var newIndexCardTime = newSelectedIndexCard.get('time_created')
				if (!newSelectedIndexCard || Math.abs(anIndexCardTime-thisSelIndexCardTime) < Math.abs(newIndexCardTime-thisSelIndexCardTime)) newSelectedIndexCard = anIndexCard

			})

		    this.set('selectedIndexCard', newSelectedIndexCard);

	    }

	},


	indexCards: function() {

		return this.get('content')

	}.property('content'),



	update: function(callback) {

		var self = this

		this.findAll().done(function() {

			var sortedContent = self.get('content').sort(function(a,b) {
			    return b.get('time_created') - a.get('time_created')
			})

			self.set('content', sortedContent)
			self.get('content').setEach('resourceUrl', self._resourceUrl())
			self.get('content').setEach('controller', self)
			
			self.findSelectedIndexCard()

			if (callback) callback(1)

		}).fail(function() {

			if (callback) callback(0)

		})

	},


})




App.IndexView = Ember.View.extend({

	indexCardsBinding: null,

	add: function() {

		var newIndexCard = App.CAIndexCard.create({
			resourceUrl : App.libraryController.get('selectedLibrary.indexController')._resourceUrl()
		})

		newIndexCard.saveResource().fail( function(e) {

			console.log(e)

		}).done(function() {

			App.libraryController.get('selectedLibrary.indexController').update(function() {

				App.libraryController.set('selectedLibrary.indexController.selectedIndexCard', undefined)

			})

		})

	},


	delete: function() {

		App.libraryController.get('selectedLibrary.indexController.selectedIndexCard').destroyResource().fail(function(e) {

			console.log(e)

		}).done(function() {

			App.libraryController.get('selectedLibrary.indexController').update()

		});

	},


	update: function() {

		App.selected('propertiesController').update(function() {

			App.selected('indexController').update(function() {

				App.selected('recordController').update()

			})

		})

	},


	didInsertElement : function() {

		App.panelDidInsert(this)

	}



})




App.IndexCardView = Ember.ContainerView.extend({
	
	indexCardBinding: null,
	classNameBindings: ['isSelected'],
	classNames: ['indexCard'],

	getLabel: function(style, seperator, fallbackText, maxLength) {

		var filteredLabels = _(this.get('indexCard.labels')).filter(function(label) {
			return (label.style == style)
		})
		
		var text = (filteredLabels.length)
		? _(filteredLabels).map(function(x) { return x.value }).join(seperator)
		: fallbackText;

		return _(text).truncate(maxLength)

	},

	childViews: function() {

		var views = Ember.A([])

		this.get('indexCard.attributedLabels').forEach(function(label) {

			if (label.get('value')) {

				var hr = Ember.View.create({
					tagName: 'hr',
					insert: label.get('insert'),
					classNameBindings: ['insert']
				})
				views.pushObject(hr);

				var text = Ember.View.create({
					style: label.get('style'),
					insert: label.get('insert'),
					classNameBindings: ['style','insert'],
					template: Ember.Handlebars.compile(label.get('prepend') + label.get('value') + label.get('append'))
				})
				views.pushObject(text);

			};

		})


		var chevron = Ember.View.create({
			classNames: ['chevron'],
		})
		views.pushObject(chevron);

		return views;

		// var sections = this.get('section.controller.sections')

		// var emptyOption = Ember.View.create({
		// 	tagName: 'option',
		// 	template: Ember.Handlebars.compile('Change Position')
		// })

		// optionsViews.pushObject(emptyOption);

		// var order = 0
		// var optionLabel = ''


		// var lastOption = Ember.View.create({
		// 	tagName: 'option',
		// 	attributeBindings: ['data-order', 'data-sectionId'],
		// 	'data-order': order + 1,
		// 	template: Ember.Handlebars.compile('Last Position')
		// })
		
		// optionsViews.pushObject(lastOption);


	}.property(),


	isSelected: function() {

		return (App.selected('indexCard') == this.get('indexCard'))

	}.property('indexCard.controller.selectedIndexCard', 'indexCard'),


	click: function() {

		var self = this
		var indexController = this.get('indexCard.controller')
		
		indexController.set('selectedIndexCard', self.get('indexCard'))
		App.inspectorController.inspectIndexCard();

	},

})
