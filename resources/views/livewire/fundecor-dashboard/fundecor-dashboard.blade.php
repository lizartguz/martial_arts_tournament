
<div wire:poll.660s="getData">
   
<table>
  <colgroup>
    <col><col><col><col><col><col>
  </colgroup>
  <thead>
    <tr style="height: 11%; background-color:none;">
      <th style="background-color:#fff; border: none; padding: 7px 0;">
        <img src="{{ asset('images/logo_dashboard2.png') }}" alt="Logo" width="40%"></th>
      <th colspan="3" style="border: none; padding: 7px 0;">
         <h4 style="text-align: left; font-size: 1.80rem;color:#1d1f41;margin-left: 1%;">Red Agrometeorológica del Valle Central de Tarija</h4>
      </th>
      <th colspan="3" style="background-color: transparent; text-align: right; padding: 7px 1% 7px 0;">
        <img src="{{ asset('images/logos_fundecor/banco_fie.png') }}" alt="banco_fie" width="10%">
        <img src="{{ asset('images/logos_fundecor/ambassade.png') }}" alt="abassade" width="10%">
        <img src="{{ asset('images/logos_fundecor/valenciana.png') }}" alt="valenciana" width="14%">
        <img src="{{ asset('images/logos_fundecor/uriondo.png') }}" alt="uriondo" width="10%">
        <img src="{{ asset('images/logos_fundecor/logo_universidad.png') }}" alt="logo_universidad" width="7%">
        <img src="{{ asset('images/logos_fundecor/petjades.png') }}" alt="petjades" width="11%">
        <img src="{{ asset('images/logos_fundecor/ecosol.png') }}" alt="ecosol" width="12%">
        <img src="{{ asset('images/logos_fundecor/nor_sur.png') }}" alt="nor_sur" width="10%">
        <img src="{{ asset('images/left_navigation_bar/logo_fundecor.png') }}" alt="Logo_fundecor" width="10%">
        
      </th>
    </tr>
  </thead>
  <tbody>
    <tr style="height:9%; background-color:none;">
      <td style="background-color: #1d1f41;">
        <div class="" style="justify-content: center; align-items: center;">
          <img src="{{ asset('images/left_navigation_bar/estaciones_on.png') }}" style="display: block; margin: 0 auto;width: 30%; filter: brightness(1) invert(0);" alt="Logo" >
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="justify-content: center; align-items: center;font-size: 0.70rem;color:white;" >ESTACIÓN</span>
        </div>
      </td>
      @foreach ($currentData as $item)
        <td  @if($loop->index==0)  style="background-color: #f7b87f;border-radius: 0px 40px 0 0;padding: 0;border:none" @else @if($loop->index == count($currentData)-1) style="background-color: #f7b87f;border-radius: 40px 40px 0 0;padding: 0;border-top: 5px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;" @else style="background-color: #f7b87f;border-radius: 40px 40px 0 0;padding: 0;border-top: 5px solid white;border-left: 5px solid white;border-right: 5px solid white;border-bottom: 0px solid white;" @endif @endif>
          <div style="padding:0.5rem;">
            <div style="font-weight: 900; font-size: 0.97rem; color: #1d1f41">
                {{ $item['name'] }}
            </div>
            <div style="font-size: 0.65rem;">
                {{ $item['location'] }} ({{ $item['latitude'] }}, {{ $item['longitude'] }})
            </div>
            <div style="font-size: 0.65rem;">
                Actualizado {{$item['dia_mes_hora_min']}}Hrs
            </div>
          </div> 
        </td>
      @endforeach
      <td rowspan="6" style="border:none">
        <div wire:ignore id="map" style="width: 100%; height: 100%; position: relative;"></div>
      </td>
    </tr>
    
   <tr style="height:15%; background-color:none;">
      <td style="background-color: #1d1f41;">
        <div class="" style="justify-content: center; align-items: center;">
          <img src="{{ asset('images/left_navigation_bar/reportes_off.png') }}" style="display: block; margin: 0 auto;width: 30%; filter: brightness(1) invert(0);" alt="Logo" >
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="justify-content: center; align-items: center;font-size: 0.70rem;color:white;" >DATOS ACTUALES</span>
        </div>
      </td>
      @foreach ($currentData as $item)
        <td @if($loop->index==0)  style="background-color: #f4c7a2;border-radius: 0px 0px 0 0;padding: 0;border:none" @else style="background-color: #f4c7a2;border-radius: 0px 0px 0 0;padding: 0;border-top: 0px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;" @endif>
         
         <table style="width: 100%; height: 100%; table-layout: fixed; border-collapse: collapse;border:none;">
            
            <tr style="height: 45%;">
               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;" >
               <img src="{{ asset('images/report/temperatura.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Temperatura">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['tempout'], 1) }}<span style="font-size: 75%;">°C</span></span>
               </div>
               </td>

               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;">
               <img src="{{ asset('images/report/lluvia.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Lluvia">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['raintotal'], 1) }}<span style="font-size: 75%;">mm</span></span>
               </div>
               </td>

               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;">
               <img src="{{ asset('images/report/viento.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Viento">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['windspeed'], 1) }}<span style="font-size: 75%;">km/h</span></span>
               </div>
               </td>
            </tr>

            <tr style="height: 45%;">
               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;">
               <img src="{{ asset('images/report/humedad.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Humedad">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['humout'], 1) }}<span style="font-size: 75%;">%</span></span>
               </div>
               </td>

               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;">
               <img src="{{ asset('images/report/delta_t.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Delta T">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['deltat'], 1) }}<span style="font-size: 75%;">°C</span></span>
               </div>
               </td>

               
               <td style="width: 33.33%; text-align: center;border-top: none;border-bottom: none;">
               <img src="{{ asset('images/report/punto_rocio.png') }}" style="width: 20%; filter: brightness(0) invert(0);" alt="Punto de Rocío">
               <div>
                  <span style="font-size: 90%;font-weight: bold;">{{ round($item['dewptout'], 1) }}<span style="font-size: 75%;">°C</span></span>
               </div>
               </td>
            </tr>
         </table>
         </td>
      @endforeach     
   </tr>
    
    <tr style="height:40%; background-color:none;">
      <td style="vertical-align: center;background-color: #1d1f41;">
        <div style="justify-content: center; align-items: center;">
          <img src="{{ asset('images/left_navigation_bar/pronosticos_off.png') }}" style="display: block; margin: 0 auto;width: 30%; filter: brightness(1) invert(0);" alt="Logo">
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="display: block; text-align: center; font-size: 0.70rem; color: white;">PRONÓSTICOS</span>
        </div>
      </td>
      @if(count($forecast) < 4)
        @for($i=0; $i<4;$i++)
          <td @if($i==0)  style="background-color: #f7d5bc;border-radius: 0px 0px 0 0;padding: 0;border:none" @else style="background-color: #f7d5bc;border-radius: 0px 0px 0 0;padding: 0;border-top: 0px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;" @endif >
            <div style="display: flex; width: 100%; box-sizing: border-box; padding: 0 2%;">
              <!-- 1. Imagen -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/sol.png') }}" style="width: 25%; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
              <!-- 2. Separador -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 2%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
              <!-- 3. Imagen -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none">
                <img src="{{ asset('images/report/temperatura.png') }}" style="width: 25%; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
              <!-- 4. Separador -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
                <div style="height: 24px; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
              <!-- 5. Imagen -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/lluvia.png') }}" style="width: 25%; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
              <!-- 6. Separador -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 2%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
              <!-- 7. Imagen -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/viento.png') }}" style="width: 25%; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
              <!-- 8. Separador -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 2%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
              <!-- 9. Imagen -->
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/humedad.png') }}" style="width: 25%; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
            </div>
          </td>
        @endfor
      @else   
        @for($i=0; $i<count($forecast);$i++)
          <td  @if($i==0)  style="background-color: #f7d5bc;border-radius: 0px 0px 0 0;padding: 0;border:none" @else style="background-color: #f7d5bc;border-radius: 0px 0px 0 0;padding: 0;border-top: 0px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;" @endif>
            <div style="display: flex; width: 100%; box-sizing: border-box; padding: 0 2%;">
             
              <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/sol.png') }}" style="width: 24px; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
             
              <div style="flex: 2; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 50%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
             
              <div style="flex: 2; display: flex; justify-content: center; align-items: center; background-color: none">
                <img src="{{ asset('images/report/temperatura.png') }}" style="width: 24px; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
             
              <div style="flex: 2; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 50%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
           
              <div style="flex: 2; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/lluvia.png') }}" style="width: 24px; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
              
              <div style="flex: 2; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 50%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
           
              <div style="flex: 2; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/viento.png') }}" style="width: 24px; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
             
              <div style="flex: 2; display: flex; justify-content: center; align-items: center;">
                <div style="width: 1px; height: 50%; background-color: rgb(105, 105, 105); transform: scaleX(1);"></div>
              </div>
             
              <div style="flex: 2; display: flex; justify-content: center; align-items: center; background-color: none;">
                <img src="{{ asset('images/report/humedad.png') }}" style="width: 24px; filter: brightness(0.3) invert(0);" alt="Logo">
              </div>
            </div>
            @php $e=0; @endphp 
            @for($e=0; $e<count($forecast[$i]);$e++)
              <hr style="width: 90%; margin: 5px auto; border: none; border-top: 1px solid #000;">
              <div style="background-color:none;display: flex; width: 100%; box-sizing: border-box; padding: 0 2%;">
               
                <div style="flex: 1; display: flex; justify-content: left; align-items: center; background-color: none;">
                   <span style="font-size: 100%;" >{{$forecast[$i][$e]['dia_literal']}}<span style="font-size: 75%;" > {{$forecast[$i][$e]['dia_mes']}}</span></span>
                </div>
               
                
              </div>


              <div style="background-color:none;display: flex; width: 100%; box-sizing: border-box; padding: 0 2%;">
               
                <div style="flex: 1; display: flex; justify-content: center; align-items: center; background-color: none;">
                  <img src="{{ asset('images/icon_forecasts/'.$forecast[$i][$e]["icon"].'.png') }}" style="width: 70%; filter: brightness(0.3) invert(0);" alt="Logo">
                </div>
               
                <div style="flex: 2; display: flex; justify-content: center; align-items: center;background-color: none;">
                  <span style="font-size:100%;" >{{round($forecast[$i][$e]['tmax'])}}│{{round($forecast[$i][$e]['tmin'])}}<span style="font-size: 75%;" >°C</span></span>
                </div>
              
                <div style="flex: 2; display: flex; justify-content: center; align-items: center;background-color: none;">
                  <span style="font-size:100%;" >{{round($forecast[$i][$e]['prec_total'],1)}}<span style="font-size: 75%;" >mm</span></span>
                </div>
               
                <div style="flex: 2; display: flex; justify-content: center; align-items: center;background-color: none;">
                  <span style="font-size: 100%;" >{{round($forecast[$i][$e]['v10m_max'])}}<span style="font-size: 75%;" >km/h</span></span>
                </div>
                
               
                <div style="flex: 2; display: flex; justify-content: center; align-items: center;background-color: none;">
                  <span style="font-size: 100%;" >{{round($forecast[$i][$e]['hr2m_max'])}}│{{round($forecast[$i][$e]['hr2m_min'])}}<span style="font-size:75%;" >%</span></span>
                </div>
              </div>
                
            @endfor
          </td>
        @endfor
      @endif
    </tr>
    <tr style="height:15%; background-color:none;">
      <td style="background-color: #1d1f41;">
        <div class="" style="justify-content: center; align-items: center;">
          <img src="{{ asset('images/left_navigation_bar/alertas_off.png') }}" style="display: block; margin: 0 auto;width: 30%; filter: brightness(1) invert(0);" alt="Logo" >
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="justify-content: center; align-items: center;font-size: 0.70rem;color:white;" >ALERTAS</span>
        </div>
      </td>
      @for ($i=0; $i < count($alerts); $i++)
        <td @if($i==0)  style="background-color: #f7e4d7;border-radius: 0px 0px 0 0;padding: 0;border:none" @else style="background-color: #f7e4d7;border-radius: 0px 0px 0 0;padding: 0;border-top: 0px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;" @endif>
        @foreach ($alerts[$i] as $item)
          <div style="font-size: 0.75rem; text-align: left; padding: 0 0.75rem;">
            <img src="{{ asset('images/left_navigation_bar/alerta_icono.png') }}"
                style="width: 14px; vertical-align: middle; display: inline-block; margin-right: 6px; filter: brightness(1) invert(0);"
                alt="Icono">
            {{ $item['reg_date'] }} Hrs - {{ $item['description'] }}
          </div>
        @endforeach
        </td>
      @endfor
    </tr>
    <tr  style="height:10%; background-color:none;">
      <td style="background-color: #1d1f41;">
        <div class="" style="justify-content: center; align-items: center;">
          <img src="{{ asset('images/left_navigation_bar/horas_frio.png') }}" style="display: block; margin: 0 auto;width: 30%; filter: brightness(1) invert(0);" alt="Logo" >
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="justify-content: center; align-items: center;font-size: 0.70rem;color:white;" >HORAS FRIO</span>
        </div>
        <div style="display: flex; justify-content: center;">
          <span style="justify-content: center; align-items: center;font-size: 0.65rem;color:white;" >{{$monthText}}</span>
        </div>
      </td>
      @foreach ($chillingHours as $item)
        <td  @if($loop->index==0)  style="background-color: #f9efe8;border-radius: 0px 0px 0 0;padding: 0;border:none;font-size: 1rem;text-align: left; padding: 0 0.75rem;" @else style="background-color: #f9efe8;border-radius: 0px 0px 0 0;padding: 0;border-top: 0px solid white;border-left: 5px solid white;border-right: 0px solid white;border-bottom: 0px solid white;font-size: 1rem;text-align: left; padding: 0 0.75rem;" @endif  >{{round($item)}} HF</td>
      @endforeach
    </tr>
  </tbody>
</table>

</div>