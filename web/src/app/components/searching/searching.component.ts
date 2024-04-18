import { Component, OnInit } from '@angular/core';
import { SpinnerComponent } from '../spinner/spinner.component';
import { Router } from '@angular/router';
import { JuegoService } from '../../services/juego.service';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
(window as any).Pusher = Pusher


@Component({
  selector: 'app-searching',
  standalone: true,
  imports: [SpinnerComponent],
  templateUrl: './searching.component.html',
  styleUrl: './searching.component.css'
})
export class SearchingComponent implements OnInit {

  constructor(private router: Router, private js: JuegoService) { }

  public echo: Echo = new Echo({
    broadcaster:'pusher',
    key:'123',
    cluster:'mt1',
    wsHost:'localhost',
    wsPort:6001,
    forceTLS:false,
    disableStatus:true,
  })

  websocket() {
    this.echo.channel('evento-juego').listen('CrearJuego', (event: any) => {
      this.router.navigate(['/juego']);
      console.log('Juego creado:', event);
    });
    console.log(this.echo)
    this.echo.connect()
  }



  ngOnInit(): void {
    this.websocket()

    this.js.buscarPartida().subscribe(
      (response) => {
        console.log(response) 
      }
    );    
  }

}
