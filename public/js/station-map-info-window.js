/* Constructor del popup de estaciones para Google Maps.
   Se separa del mapa principal para facilitar cambios visuales y de contenido. */

(function registerStationMapInfoWindow(window) {
    const styles = Object.freeze({
        label: 'color: #000000; font-size: 14px; font-weight: 600;',
        value: 'font-size: 14px;',
        agroLabel: 'color: #000000; font-size: 13px; font-weight: 600;',
        agroValue: 'font-size: 13px;',
        receiptBadge: 'position: absolute; top: 12px; right: 12px; padding: 4px 10px; border-radius: 20px; font-size: 12px; color: #ffffff;',
    });

    const OFFLINE_RECEIPT_THRESHOLD_HOURS = 24 * 14;
    const SHOW_INFO_WINDOW_ACTIONS = false;

    // Calcula las horas transcurridas desde el ultimo dato recibido.
    function getReceiptAgeHours(receiptDate) {
        if (!receiptDate) {
            return null;
        }

        const DateTime = window.luxon?.DateTime;
        if (DateTime) {
            const receivedAt = DateTime.fromFormat(receiptDate, 'yyyy-MM-dd HH:mm:ss', {
                zone: 'America/La_Paz',
            });

            return receivedAt.isValid
                ? DateTime.now().setZone('America/La_Paz').diff(receivedAt, 'hours').hours
                : null;
        }

        const parsedDate = new Date(receiptDate.replace(' ', 'T'));
        if (Number.isNaN(parsedDate.getTime())) {
            return null;
        }

        return (Date.now() - parsedDate.getTime()) / (1000 * 60 * 60);
    }

    // Devuelve el estilo del badge de fecha segun si la estacion esta caida.
    function resolveReceiptBadgeStyle(station) {
        const receiptAgeHours = getReceiptAgeHours(station.receipt_date);
        const isOffline = receiptAgeHours !== null && receiptAgeHours >= OFFLINE_RECEIPT_THRESHOLD_HOURS;
        const backgroundColor = isOffline ? '#dc2626' : 'rgba(255,255,255,0.2)';

        return `${styles.receiptBadge} background-color: ${backgroundColor};`;
    }

    // Renderiza acciones del popup cuando vuelvan a estar habilitadas.
    function buildActionButtons(station, infoWindowTranslations) {
        if (!SHOW_INFO_WINDOW_ACTIONS) {
            return '';
        }

        return `
                <!-- Botones de accion -->
                <div style="background-color: #f8f9fa; padding: 10px; display: flex; gap: 8px; justify-content: center; border-bottom: 1px solid #e0e0e0;">
                    <button onclick="Livewire.dispatch('openMoreDataModal', [${station.stationId}])" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 6px; padding: 9px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-chart-line" style="margin-right: 5px;"></i>${infoWindowTranslations.infoWindow.buttons.moreData}
                    </button>
                    <button onclick="Livewire.dispatch('openStationInfoModal', [${station.stationId}])" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border: none; border-radius: 6px; padding: 9px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>${infoWindowTranslations.infoWindow.buttons.viewAll}
                    </button>
                </div>`;
    }

    // Crea el HTML de la ventana informativa de una estacion.
    function buildContent({
        station,
        direction1 = '',
        direction2 = '',
        qnhText = null,
        translations = window.stationMapTranslations,
    }) {
        const infoWindowTranslations = translations;

        return `
            <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px; width: 600px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <!-- Header -->
                <div style="background: linear-gradient(135deg, #1e5e43 0%, #276d44 100%); color: white; padding: 16px; position: relative;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="background-color: rgba(255,255,255,0.2); padding: 10px; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt" style="font-size: 24px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 20px; font-weight: 600; margin-bottom: 4px;">${station.name}</div>
                            <div style="font-size: 14px; opacity: 0.95;"><i class="fas fa-map-pin" style="margin-right: 5px;"></i>${station.location}</div>
                            <div style="font-size: 13px; opacity: 0.9; margin-top: 3px;"><i class="fas fa-globe" style="margin-right: 5px;"></i>LAT: ${station.latitude} / LON: ${station.longitude}</div>
                        </div>
                    </div>
                    <div style="${resolveReceiptBadgeStyle(station)}">
                        <i class="far fa-clock" style="margin-right: 4px;"></i>${station.receipt_date}
                    </div>
                </div>

                ${buildActionButtons(station, infoWindowTranslations)}

                <!-- Contenido principal -->
                <div style="background-color: white; padding: 15px;">
                    <!-- Variables Meteorologicas -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <!-- Columna 1 -->
                        <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #3968a6;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-thermometer-half" style="color: #e74c3c; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.temperature}</span>
                                    <strong style="color: #e74c3c; ${styles.value}">${station.tempout || 'NA'} °C</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-tint" style="color: #3498db; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.humidity}</span>
                                    <strong style="color: #3498db; ${styles.value}">${station.humout || 'NA'} %</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-temperature-low" style="color: #9b59b6; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.dewPoint}</span>
                                    <strong style="color: #9b59b6; ${styles.value}">${station.dewptout || 'NA'} °C</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-compass" style="color: #16a085; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.windDirection}</span>
                                    <strong style="color: #16a085; ${styles.value}">${station.winddir || 'NA'}° ${direction1 || ''}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-wind" style="color: #2ecc71; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.windSpeed}</span>
                                    <strong style="color: #2ecc71; ${styles.value}">${station.windspeed || 'NA'}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-bolt" style="color: #f39c12; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.gust}</span>
                                    <strong style="color: #f39c12; ${styles.value}">${station.windspeedhi || 'NA'}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-cloud-rain" style="color: #3498db; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.precipitation}</span>
                                    <strong style="color: #3498db; ${styles.value}">${station.raintotal || 'NA'} mm</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0;">
                                    <span style="${styles.label}"><i class="fas fa-tint" style="color: #3498db; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.intensity}</span>
                                    <strong style="color: #3498db; ${styles.value}">${station.rainrate || 'NA'} mm/h</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div style="background-color: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #27ae60;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-tachometer-alt" style="color: #34495e; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.barometricPressure}</span>
                                    <strong style="color: #34495e; ${styles.value}">${station.press || 'NA'} mb</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-compass" style="color: #16a085; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.windDirection2}</span>
                                    <strong style="color: #16a085; ${styles.value}">${station.winddir2 || 'NA'}° ${direction2 || ''}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-wind" style="color: #2ecc71; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.windSpeed2}</span>
                                    <strong style="color: #2ecc71; ${styles.value}">${station.windspeed2 || 'NA'}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-bolt" style="color: #f39c12; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.gust2}</span>
                                    <strong style="color: #f39c12; ${styles.value}">${station.windspeedhi2 || 'NA'}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-sun" style="color: #f1c40f; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.solarRadiation}</span>
                                    <strong style="color: #f1c40f; ${styles.value}">${station.uvindex || 'NA'} W/m²</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="${styles.label}"><i class="fas fa-water" style="color: #3498db; margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.labels.evapotranspiration}</span>
                                    <strong style="color: #3498db; ${styles.value}">${station.solevo || 'NA'} ${infoWindowTranslations.infoWindow.units.evapotranspiration}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seccion Agro -->
                    <div style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); padding: 12px; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid #4caf50;">
                        <div style="font-weight: 600; color: #2e7d32; margin-bottom: 10px; font-size: 14px;">
                            <i class="fas fa-seedling" style="margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.sections.agriculturalSensors}
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.soilTemp1}</span>
                                <strong style="color: #2e7d32; ${styles.agroValue}">${station.soiltemp1 || 'NA'} °C</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.soilTemp2}</span>
                                <strong style="color: #2e7d32; ${styles.agroValue}">${station.soiltemp2 || 'NA'} °C</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.soilHum1}</span>
                                <strong style="color: #1976d2; ${styles.agroValue}">${station.soilhum1 || 'NA'} %</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.soilHum2}</span>
                                <strong style="color: #1976d2; ${styles.agroValue}">${station.soilhum2 || 'NA'} %</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.leafTemp1}</span>
                                <strong style="color: #388e3c; ${styles.agroValue}">${station.leaftemp1 || 'NA'} °C</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.leafTemp2}</span>
                                <strong style="color: #388e3c; ${styles.agroValue}">${station.leaftemp2 || 'NA'} °C</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.leafHum1}</span>
                                <strong style="color: #1976d2; ${styles.agroValue}">${station.leafhum1 || 'NA'} %</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 5px; background-color: white; border-radius: 4px;">
                                <span style="${styles.agroLabel}">${infoWindowTranslations.infoWindow.labels.leafHum2}</span>
                                <strong style="color: #1976d2; ${styles.agroValue}">${station.leafhum2 || 'NA'} %</strong>
                            </div>
                        </div>
                    </div>

                    ${qnhText ? `
                    <!-- Seccion Aviacion -->
                    <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px; border-radius: 8px; border-left: 4px solid #2196f3;">
                        <div style="font-weight: 600; color: #1565c0; margin-bottom: 8px; font-size: 14px;">
                            <i class="fas fa-plane" style="margin-right: 6px;"></i>${infoWindowTranslations.infoWindow.sections.aviationVariables}
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 8px; background-color: white; border-radius: 4px;">
                            <span style="${styles.label}">${infoWindowTranslations.infoWindow.labels.qnh}</span>
                            <strong style="color: #1565c0; ${styles.value}">${qnhText}</strong>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>`;
    }

    window.StationMapInfoWindow = Object.freeze({
        buildContent,
    });
})(window);
