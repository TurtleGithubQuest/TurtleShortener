declare const echarts: any;
declare const translations: any;
declare const geoDataRangeSummary: any;
declare const geoDataSummary: {
    total_clicks: number;
    avg_clickTime: number;
    countries: GeoItem[];
    cities: GeoItem[];
    operating_systems: SystemItem[];
    clicks_by_day: DayClickItem[];
} | undefined;

interface ChartOption {
    title?: {
        text: string;
        left: string;
    };
    series: any[];
    xAxis?: {
        data: string[];
    };
    radar?: {
        name?: {
            rich: Record<string, any>;
        };
        indicator?: Array<{
            icon: string;
            name: string;
            max: number;
        }>;
    };
}

type ChartTheme = "pie" | "radar" | "bar" | "line";
type ChartReturn = [HTMLDivElement, any];