OneCRMFormEditor = new function() {

	var $ = jQuery;
	var editor = this;

	this.fields_meta = {
		text: [
			{
				name: 'size',
				label: 'Size (optional)',
				type: 'number',
				min: 1
			},
			{
				name: 'maxlen',
				label: 'Maximum length (optional)',
				type: 'number',
				min: 1
			},
			{
				name: 'default',
				label: 'Default value (optional)',
				type: 'text'
			},
			{
				name: 'placeholder',
				label: 'Placeholder for empty value (optional)',
				type: 'text'
			},
			{
				name: 'validator',
				label: 'Validator regular expression (optional)',
				type: 'text'
			},
			{
				name: 'validator_message',
				label: 'Validator message (optional)',
				type: 'text'
			}
		],
		spinbox : [
			{
				name: 'min',
				label: 'Minimum value (optional)',
				type: 'number'
			},
			{
				name: 'max',
				label: 'Maximum value (optional)',
				type: 'number'
			},
			{
				name: 'step',
				label: 'Step (optional)',
				type: 'number'
			},
			{
				name: 'default',
				label: 'Default value (optional)',
				type: 'text'
			},
			{
				name: 'placeholder',
				label: 'Placeholder for empty value (optional)',
				type: 'text'
			}
		],
		date: [
			{
				name: 'default',
				label: 'Default value (optional)',
				type: 'text'
			},
			{
				name: 'placeholder',
				label: 'Placeholder for empty value (optional)',
				type: 'text'
			}
		],
		checkbox: [
			{
				name: 'label',
				label: 'Label (optional)',
				type: 'text'
			},
			{
				name: 'label_first',
				label: 'Place label before checkbox',
				type: 'bool'
			},
			{
				name: 'use_label',
				label: 'Use <label> tag',
				type: 'bool'
			}
		],
		textarea: [
			{
				name: 'cols',
				label: 'Columns (optional)',
				type: 'number',
				min: 1
			},
			{
				name: 'rows',
				label: 'Rows (optional)',
				type: 'number',
				min: 1
			},
			{
				name: 'maxlen',
				label: 'Max. length (optional)',
				type: 'number',
				min: 1
			},
			{
				name: 'default',
				label: 'Default value (optional)',
				type: 'text'
			},
			{
				name: 'placeholder',
				label: 'Placeholder for empty value (optional)',
				type: 'text'
			}
		],
		select: [
			{
				name: 'choices',
				label: 'Choices',
				type: 'textarea'
			},
			{
				name: 'multiple',
				label: 'Allow multiple selections',
				type: 'bool'
			},
			{
				name: 'add_blank',
				label: 'Insert a blank item as the first option',
				type: 'bool'
			}
		],
		radio: [
			{
				name: 'choices',
				label: 'Choices',
				type: 'textarea'
			},
			{
				name: 'separator',
				type: 'text',
				label : 'Buttons separator (optional)',
				'default': '<br />'
			},
			{
				name: 'label_first',
				label: 'Place label before radio button',
				type: 'bool'
			},
			{
				name: 'use_label',
				label: 'Use <label> tag',
				type: 'bool'
			}
		],
		submit: [
			{
				name: 'label',
				label: 'Label (optional)',
				type: 'text'
			},
			{
				name: 'wait_label',
				label: 'Label when waiting for reply  (optional)',
				type: 'text'
			}
		],
		hidden: [
			{
				name: 'value',
				label: 'Value',
				type: 'text'
			}
		]
	};

	this.onecrm_case_field_custom = {
		name : 'onecrm_case_field_custom',
		label: 'Custom Field',
		type: 'text',
		postrender: function(input, inputContainer, f, def) {
			inputContainer.style.display = f['onecrm_case_field'] == '_' ? '' : 'none';
		}
	};
	this.onecrm_field_custom = {
		name : 'onecrm_field_custom',
		label: 'Custom Field',
		type: 'text',
		postrender: function(input, inputContainer, f, def) {
			inputContainer.style.display = f['onecrm_field'] == '_' ? '' : 'none';
		}
	};
	this.lg_field_custom = {
		name : 'lg_field_custom',
		label: 'Custom Field',
		type: 'text',
		postrender: function(input, inputContainer, f, def) {
			inputContainer.style.display = f['lg_field'] == '_' ? '' : 'none';
		}
	};
	this.onecrm_case_field = {
		'name' : 'onecrm_case_field',
		'type' : 'dropdown',
		'label' : 'Map to 1CRM Case field',
		onchange: function() {
			$(this.parentNode.parentNode).find('.form-group-onecrm_case_field_custom').css({display : this.value != '_' ? 'none' : ''});
		},
		'options' : [
			['', '-- None --'],
			['_', '-- Custom --'],
			['type', 'Type'],
			['name', 'Subject'],
			['status', 'Status'],
			['priority', 'Priority'],
			['cust_phone_no', 'Contact Phone'],
			['refered_by', 'Referred By'],
			['description', 'Description']
		]
	}
	
	this.onecrm_field = {
		'name' : 'onecrm_field',
		'type' : 'dropdown',
		'label' : 'Map to 1CRM Lead field',
		onchange: function() {
			$(this.parentNode.parentNode).find('.form-group-onecrm_field_custom').css({display : this.value != '_' ? 'none' : ''});
		},
		'options' : [
			['', '-- None --'],
			['_', '-- Custom --'],
			['temperature', 'Temperature'],
			['salutation', 'Salutation'],
			['first_name', 'First Name'],
			['last_name', 'Last Name'],
			['title', 'Title'],
			['refered_by', 'Referred By'],
			['lead_source', 'Lead Source'],
			['lead_source_description', 'Lead Source Description'],
			['status', 'Status'],
			['status_description', 'Status Description'],
			['department', 'Department'],
			['do_not_call', 'Do Not Call'],
			['phone_home', 'Phone Home'],
			['phone_mobile', 'Phone Mobile'],
			['phone_work', 'Phone Work'],
			['phone_other', 'Phone Other'],
			['phone_fax', 'Fax'],
			['skype_id', 'Skype ID'],
			['email1', 'Primary Email'],
			['email2', 'Alternate Email'],
			['email_opt_out', 'Email Opt Out'],
			['website', 'Website'],
			['primary_address_street', 'Primary Address Street'],
			['primary_address_city', 'Primary Address City'],
			['primary_address_state', 'Primary Address State'],
			['primary_address_postalcode', 'Primary Address Postal Code'],
			['primary_address_country', 'Primary Address Country'],
			['alt_address_street', 'Alternate Address Street'],
			['alt_address_city', 'Alternate Address City'],
			['alt_address_state', 'Alternate Address State'],
			['alt_address_postalcode', 'Alternate Address Postal Code'],
			['alt_address_country', 'Alternate Address Country'],
			['description', 'Description'],
			['account_name', 'Account Name'],
			['account_description', 'Account Description']
		]
	}
	
	this.lg_field = {
		'name' : 'lg_field',
		'label' : 'Map to Lead Guerrilla field',
		'type' : 'dropdown',
		onchange: function() {
			$(this.parentNode.parentNode).find('.form-group-lg_field_custom').css({display : this.value != '_' ? 'none' : ''});
		},
		'options' : [
			['', '-- None --'],
			['_', '-- Custom --'],
			['title', 'Title'],
			['firstname', 'First Name'],
			['lastname', 'Last Name'],
			['email', 'Email'],
			['company', 'Company'],
			['phone', 'Phone'],
			['mobile', 'Mobile Phone'],
			['fax', 'Fax'],
			['address1', 'Address Line 1'],
			['address2', 'Address Line 2'],
			['city', 'City'],
			['state', 'State'],
			['zipcode', 'ZIP Code'],
			['country', 'Country'],
			['website', 'Website']
		]
	}
	

	this.fields_meta.email = this.fields_meta.text;
	this.fields_meta.url = this.fields_meta.text;
	this.fields_meta.phone = this.fields_meta.text;
	this.fields_meta.slider = this.fields_meta.spinbox;

	this.field_type_titles = {
		'text' : 'Text field',
		'email' : 'Email',
		'url' : 'URL',
		'phone' : 'Telephone number',
		'spinbox' : 'Number (spinbox)',
		'slider' : 'Number (slider)',
		'date' : 'Date',
		'textarea' : 'Text area',
		'select' : 'Drop-down menu',
		'checkbox' : 'Checkbox',
		'radio' : 'Radio buttons',
		'hidden' : 'Hidden field',
		'submit' : 'Submit button'
	};

	this.no_req_opt = {
		'radio' : 1,
		'submit' : 1,
		'hidden' : 1
	};

	this.init = function(fields) {
		$('#field_types_list div').on('click', this.add_field);
		editor.fields = fields || [];
		postboxes.add_postbox_toggles(_ocrmf.screenId);
		editor.render();
		$('.field-container .handle').parent().parent().addClass('closed');
	};

	this.add_field = function() {
		$('#field_types_list').hide();
		var type = $(this).attr('data-id');
		var field = {
			type: type,
			name: type
		};
		editor.fields.push(field);
		editor.render();
		$('.field-container .handle').parent().parent().addClass('closed');
		$('#field-handle-' + (editor.fields.length - 1)).parent().parent().removeClass('closed');
	};

	this.render = function() {
		$('#fields-container *').remove();
		var container = $('#fields-container')[0];
		for (var i = 0; i < editor.fields.length; i++) {
			var wrapper = editor.createElement('div', {'class' : 'field-container'});
			container.appendChild(wrapper);
			var title = editor.createElement('div', {'class': 'field-title'});
			wrapper.appendChild(title);
			var handle = editor.createElement('div', {'class': 'handle', id: 'field-handle-' + i});
			handle.appendChild(editor.createElement('span'));
			title.appendChild(handle);
			
			var insText = editor.createElement('div', {'class': 'insert-into-text', title: 'Insert field into form'});
			insText.appendChild(editor.createText('A'));
			var insEmail = editor.createElement('div', {'class': 'insert-into-text', title: 'Insert field into email text'});
			insEmail.appendChild(editor.createText('@'));
			title.appendChild(insEmail);
			title.appendChild(insText);
			
			var nameWrapper = editor.createElement('div', {'class': 'field-name-wrapper'});
			title.appendChild(nameWrapper);
			var nameInput = editor.createElement('input', {type: 'text', title: 'Field name'});
			var f = editor.fields[i];
			nameInput.value = f.name;
			nameWrapper.appendChild(nameInput);

			var fun = function(i) {
				var f = editor.fields[i];
				$(nameInput).on('change', function() {
					var val = $(this).val();
					val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
					if (val == '') {
						var rand = Math.floor(Math.random() * 1000);
						val = f.type + '-' + rand;
					}
					$(this).val(val);
					f.name = $(this).val();
				});
				$(insText).on('click', function() {
					editor.insertText('{' + f.name + '}', $('#ocrmf-form')[0]);
				});
				$(insEmail).on('click', function() {
					editor.insertText('{' + f.name + '}', $('#ocrmf-body')[0]);
				});
			}
			fun(i);

			var inputsContainer = editor.createElement('div', {'class' : 'field-description'});
			wrapper.appendChild(inputsContainer);

			editor.render_field(i, f, inputsContainer);
		}
	

		$('.field-container .handle').on('click', function() {
			var clicked = this;
			$('.field-container .handle').each(function() {
				if (this != clicked) $(this).parent().parent().addClass('closed');
			});
			$(this).parent().parent().toggleClass('closed');
		});
	};

	this.insertText = function(content, input) {
		if ( document.selection ) { //IE
			input.focus();
			sel = document.selection.createRange();
			sel.text = content;
			input.focus();
		} else if ( input.selectionStart || input.selectionStart === 0 ) { // FF, WebKit, Opera
			text = input.value;
			startPos = input.selectionStart;
			endPos = input.selectionEnd;
			scrollTop = input.scrollTop;

			input.value = text.substring(0, startPos) + content + text.substring(endPos, text.length);

			input.focus();
			input.selectionStart = startPos + content.length;
			input.selectionEnd = startPos + content.length;
			input.scrollTop = scrollTop;
		} else {
			input.value += content;
			input.focus();
		}
	};

	this.render_field = function(idx, f, container) {
		var div = editor.createElement('div', {style: 'float:right'});
		var span = editor.createElement('span', {'class' : 'closebutton', title: 'Delete'});
		span.appendChild(editor.createText('x'));
		$(span).on('click', function() {
			editor.delete_field(idx);
		});
		div.appendChild(span);
		container.appendChild(div);

		var h3 = editor.createElement('h3');
		h3.appendChild(editor.createText(editor.field_type_titles[f.type]));
		container.appendChild(h3);
		var meta = editor.fields_meta[f.type].map(function(el) {return el});
		if (f.type != 'submit') {
			meta.push(editor.onecrm_field);
			meta.push(editor.onecrm_field_custom);
			meta.push(editor.onecrm_case_field);
			meta.push(editor.onecrm_case_field_custom);
			meta.push(editor.lg_field);
			meta.push(editor.lg_field_custom);
		}
		if (!editor.no_req_opt[f.type])
			editor.render_bool(
				f, container, {name: 'required', type: 'bool', label: 'Required field'},
				function() {
					var val = $(this).val();
					val = val.replace(/[^0-9a-zA-Z:._-]/g, '');
					$(this).val(val);
				}
			);
		editor.render_text(
			f, container, {name: 'id', type: 'text', label: 'id (optional)'},
			function() {
				var val = $(this).val();
				val = val.replace(/[^0-9a-zA-Z:._-]/g, '');
				$(this).val(val);
			}
		);
		if (f.type != 'hidden')
			editor.render_text(
				f, container, {name: 'class', type: 'text', label: 'class (optional)'},
				function() {
					var val = $(this).val();
					val = val.replace(/[^0-9a-zA-Z:._-]/g, '');
					$(this).val(val);
				}
			);
		for (var i = 0; i < meta.length; i++) {
			var def = meta[i];
			switch (def.type) {
				case 'number' :
					editor.render_number(f, container, def);
					break;
				case 'textarea' :
					editor.render_textarea(f, container, def);
					break;
				case 'text' :
					editor.render_text(f, container, def);
					break;
				case 'bool' :
					editor.render_bool(f, container, def);
					break;
				case 'dropdown' :
					editor.render_dropdown(f, container, def);
					break;
			}
		}
	};

	this.render_number = function(f, container, def) {
		var p = editor.createElement('p');
		p.appendChild(editor.createText(def.label));
		p.appendChild(editor.createElement('br'));
		var input = editor.createElement('input', {type: 'number', min: def.min, max: def.max, value: f[def.name] || ''});
		p.appendChild(input);
		$(input).on('change', function() {
			f[def.name] = this.value;
		});
		container.appendChild(p);
	};

	this.render_text = function(f, container, def, onchange) {
		var p = editor.createElement('p', {'class': 'form-group-' + def.name});
		p.appendChild(editor.createText(def.label));
		p.appendChild(editor.createElement('br'));
		var input = editor.createElement('input', {type: 'text', value: f[def.name] || def['default'] || ''});
		p.appendChild(input);
		if (onchange)
			$(input).on('change', onchange);

		$(input).on('change', function() {
			f[def.name] = this.value;
		});
		container.appendChild(p);
		if (typeof def.postrender === 'function') {
			def.postrender(input, p, f, def);
		}
		return input;
	};

	this.render_dropdown = function(f, container, def, onchange) {
		var p = editor.createElement('p');
		p.appendChild(editor.createText(def.label));
		p.appendChild(editor.createElement('br'));
		var select = editor.createElement('select', {});

		for (var i = 0; i< def.options.length; i++) {
			var opt = def.options[i];
			var attrs = {'value' : opt[0]};
			if (f[def.name] == opt[0]) {
				attrs.selected = "selected";
			}
			var o = editor.createElement('option', attrs);
			o.appendChild(editor.createText(opt[1]));
			select.appendChild(o);
		}

		p.appendChild(select);
		if (onchange)
			$(select).on('change', onchange);
		if (def.onchange)
			$(select).on('change', def.onchange);

		$(select).on('change', function() {
			f[def.name] = this.value;
		});
		container.appendChild(p);
		return select;
	};

	this.render_textarea = function(f, container, def) {
		var p = editor.createElement('p');
		p.appendChild(editor.createText(def.label));
		p.appendChild(editor.createElement('br'));
		var input = editor.createElement('textarea', {cols: 30, rows : 7, value : f[def.name] || ''});
		input.appendChild(editor.createText(f[def.name] || ''));
		p.appendChild(input);
		$(input).on('change', function() {
			f[def.name] = this.value;
		});
		container.appendChild(p);
	};

	this.render_bool = function(f, container, def) {
		var p = editor.createElement('p');
		var l = editor.createElement('label');
		p.appendChild(l);
		var attrs = {type: 'checkbox', value: '1'};
		if (f[def.name])
			attrs.checked = true;
		var input = editor.createElement('input', attrs);
		l.appendChild(input);
		$(input).on('change', function() {
			f[def.name] = this.checked;
		});
		l.appendChild(editor.createText(def.label));
		container.appendChild(p);
	};

	this.createText = function(txt) {
		return document.createTextNode(txt);
	};

	this.createElement = function(name, attrs) {
		if (!attrs)
			attrs = {};
		var el = document.createElement(name);
		for (var i in attrs)
			el.setAttribute(i, attrs[i]);
		return el;
	};

	this.delete_field = function(idx) {
		editor.fields.splice(idx, 1);
		editor.render();
	};

	this.validate = function() {
		$('#ocrmf-fields').val(JSON.stringify(editor.fields));
		return true;
	};

	this.import = function() {
		var code = $("#ocrmf-import-box").val();
		try {
			eval('data = ' +code + ';');
			if (typeof(data.fields) == 'object') {
				editor.fields = data.fields;
				editor.render();
			}
			if (typeof(data.form) != 'undefined')
				$('#ocrmf-form').val(data.form);
			if (typeof(data.url) != 'undefined')
				$('#ocrmf-url').val(data.url);
			console.debug(data);
		} catch (e) {
			console.debug(e);
		}
	}

}();

