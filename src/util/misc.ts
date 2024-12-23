export const copyValue = function(element: HTMLElement, textToCopy: string|null = null) {
	const finalTextToCopy = textToCopy ||
                           element.getAttribute('copyValue') ||
                           element.value;

	navigator.clipboard.writeText(finalTextToCopy).then(() => {
		element.classList.add('success');
		setTimeout(() => {
			element.classList.remove('success');
		}, 500);
	});
};

export function createEl(tag: string, classNames: string|null = null) {
	const element = document.createElement(tag);
	if (classNames) {
		element.classList.add(...classNames.split(' '));
	}
	return element;
}

export function createAlert(level: string, text: string) {
	const label = createEl('label', 'alert');
	const input = createEl('input');

	input.type = 'checkbox';
	input.setAttribute('autocomplete', 'off');

	const notificationInfo = createEl('span', 'notification-info');
	notificationInfo.textContent = ' >';

	const content = createEl('span', `content ${level}`);
	content.innerHTML = text;

	label.appendChild(input);
	label.appendChild(notificationInfo);
	label.appendChild(content);

	return label;
}

export function createAlertAndAssign(level: string, text: string): void {
	const alerts = document.getElementById('alerts');
	if (alerts) {
		alerts.appendChild(createAlert(level, text));
	}
}

export function deepMerge(obj1: any, obj2: any, arrayReplace = true, mergeObjectsInArray = false) {
	const output = { ...obj1 };
	for (const key in obj2) {
		const val = obj2[key];
		if (typeof val === 'object' && val !== null) {
			if (Array.isArray(val)) {
				if (arrayReplace || !Array.isArray(output[key])) {
					output[key] = obj2[key];
				} else {
					output[key] = output[key].map((item, index) => {
						if (mergeObjectsInArray && typeof item === 'object' && typeof val[index] === 'object') {
							return deepMerge(item, val[index], arrayReplace, mergeObjectsInArray);
						}
						return val[index] !== undefined ? val[index] : item;
					});
				}
			} else {
				output[key] = deepMerge(obj1[key] || {}, obj2[key], arrayReplace, mergeObjectsInArray);
			}
		} else if (typeof val === 'function') {
			output[key] = val;
		} else {
			output[key] = obj2[key];
		}
	}
	return output;
}

export function deepClone(obj: any): any {
	if (obj === null || typeof obj !== 'object') {
		return obj;
	}

	if (Array.isArray(obj)) {
		return obj.map(deepClone);
	}

	const clonedObj: any = {};
	for (const key in obj) {
		if (Object.prototype.hasOwnProperty.call(obj, key)) {
			if (typeof obj[key] === 'function') {
				clonedObj[key] = obj[key];
			} else {
				clonedObj[key] = deepClone(obj[key]);
			}
		}
	}
	return clonedObj;
}

export 	function getShade(baseColor: string, index: number): string | null {
	if (!baseColor) {
		return null;
	}
	const rgb = parseInt(baseColor.substring(1), 16);
	const r = ((rgb >> 16) & 0xff) / 255;
	const g = ((rgb >> 8) & 0xff) / 255;
	const b = (rgb & 0xff) / 255;

	const max = Math.max(r, g, b);
	const min = Math.min(r, g, b);
	const l = (max + min) / 2;

	let h = 0;
	let s = 0;

	if (max !== min) {
		const d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

		if (max === r) {
			h = (g - b) / d + (g < b ? 6 : 0);
		} else if (max === g) {
			h = (b - r) / d + 2;
		} else if (max === b) {
			h = (r - g) / d + 4;
		}
		h /= 6;
	}

	h = (h * 360);
	s *= 100;
	const baseL = l * 100;

	const hueShift = (index * 5) % 360;
	const newH = (h + hueShift) % 360;
	const newL = Math.max(20, Math.min(80, baseL + (index * 10 - 20)));
	const newS = Math.min(100, s + 10);

	return `hsl(${newH}, ${newS}%, ${newL}%)`;
}

console.log();