declare global {
    interface Number {
        formatUnixToDate(userFriendly: boolean): string;
    }
}

export const getTimezone = (): string => {
	return Intl.DateTimeFormat().resolvedOptions().timeZone;
};

Number.prototype.formatUnixToDate = function(userFriendly: boolean): string {
	const date = new Date(this.valueOf());
	const year = date.getFullYear();
	const month = String(date.getMonth() + 1).padStart(2, '0');
	const day = String(date.getDate()).padStart(2, '0');
	const hour = String(date.getHours()).padStart(2, '0');
	const minute = String(date.getMinutes()).padStart(2, '0');

	if (userFriendly) {
		return `${day}/${month}/${year} ${hour}:${minute}`;
	}

	return `${year}-${month}-${day}T${hour}:${minute}`;
};

export const updateInputElementDate = (el: HTMLInputElement | null, unix: string | null = null): void => {
	if (el === null) {
		return;
	}
	const unixTime = unix ? Number.parseInt(unix) : Date.now();
	el.value = unixTime.formatUnixToDate(false);
};

export const updateElementTextDate = (el: HTMLElement | null, unix: string | null = null): void => {
	if (el === null) {
		return;
	}
	const unixTime = unix ? Number.parseInt(unix) : Date.now();
	el.innerHTML = unixTime.formatUnixToDate(true);
};

export {};