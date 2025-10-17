(function (window) {
    'use strict';
    let VasoftChangelog = BX.namespace('VasoftChangelog');
    VasoftChangelog.renderSection = function (data) {
        let element = document.createElement('div');
        element.classList.add('vs-changelog__section');
        let elementHeader = document.createElement('div');
        elementHeader.classList.add('vs-changelog__section-title');
        elementHeader.innerText = data.name
        element.appendChild(elementHeader);
        if (data.items.length > 0) {
            let list = document.createElement('ul');
            list.classList.add('vs-changelog__changes');
            for (let item of data.items) {
                let elementItem = document.createElement('li');
                elementItem.classList.add('vs-changelog__change');
                elementItem.innerHTML = item;
                list.appendChild(elementItem);
            }
            element.appendChild(list);
        }
        return element
    }
    VasoftChangelog.renderVersion = function (version) {
        let element = document.createElement('div');
        element.classList.add('vs-changelog__version');
        if (version.current) {
            element.classList.add('vs-changelog__version_current');
        }
        let elementHeader = document.createElement('div');
        elementHeader.classList.add('vs-changelog__version-header');

        let elementTitle = document.createElement('SPAN');
        elementTitle.classList.add('vs-changelog__title');
        elementTitle.innerText = version.version;

        let elementDate = document.createElement('SPAN');
        elementDate.classList.add('vs-changelog__date');
        elementDate.innerText = version.date;

        elementHeader.appendChild(elementTitle);
        elementHeader.appendChild(elementDate);
        element.appendChild(elementHeader);

        for (let section of version.sections) {
            let sectionElement = VasoftChangelog.renderSection(section);
            element.appendChild(sectionElement);
        }
        return element;
    }
    VasoftChangelog.options = {};
    VasoftChangelog.box = null;
    VasoftChangelog.init = function (options) {
        if (options.containerId === undefined) {
            return false;
        }
        let changelogBox = document.getElementById(options.containerId);
        if (!changelogBox) {
            return false
        }
        VasoftChangelog.options = options;
        let panel = VasoftChangelog.getPanel(options.button);
        VasoftChangelog.box = VasoftChangelog.getContentBox();
        changelogBox.appendChild(panel);
        changelogBox.appendChild(VasoftChangelog.box);
        VasoftChangelog.get('');
        return true;
    }
    VasoftChangelog.get = function (search) {
        BX.ajax.runComponentAction(VasoftChangelog.options.componentName, 'list', {
            mode: 'class',
            signedParameters: VasoftChangelog.options.signedParameters,
            data: {filter: search}
        }).then(function (response) {
            VasoftChangelog.box.innerHTML = '';
            for (let version of response.data.list) {
                let element = VasoftChangelog.renderVersion(version);
                VasoftChangelog.box.appendChild(element);
            }
        }).catch(function (response) {
            VasoftChangelog.box.innerHTML = '';
            for (let error of response.errors) {
                let errorElement = VasoftChangelog.renderError(error);
                VasoftChangelog.box.appendChild(errorElement);
            }
        });
    }
    VasoftChangelog.renderError = function (data) {
        let error = document.createElement('p');
        error.classList.add('vs-changelog__error');
        error.innerText = data.message;
        return error;

    };
    VasoftChangelog.getContentBox = function (options) {
        let box = document.createElement('div');
        box.classList.add('vs-changelog__box');
        return box;
    }
    VasoftChangelog.getPanel = function (options) {
        let panel = document.createElement('div');
        panel.classList.add('vs-changelog__panel');

        let input = document.createElement('input');
        input.classList.add('vs-changelog__input');
        input.type = 'text';

        let button = document.createElement('button');
        button.classList.add('vs-changelog__button', ...options.classes.split(' '));
        button.innerText = options.title;

        button.addEventListener('click', () => {
            VasoftChangelog.get(input.value);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                VasoftChangelog.get(input.value);
            }
        });
        panel.appendChild(input);
        panel.appendChild(button);
        return panel;
    }

})(window);