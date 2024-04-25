import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { JuegoService } from '../../services/juego.service';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { SpinnerComponent } from '../spinner/spinner.component';

(window as any).Pusher = Pusher;

@Component({
  selector: 'app-searching',
  standalone: true,
  imports: [ SpinnerComponent ],
  templateUrl: './searching.component.html',
  styleUrl: './searching.component.css'
})

export class SearchingComponent implements OnInit, OnDestroy {

  constructor(private router: Router, private js: JuegoService) { }

  public echo: Echo = new Echo({
    broadcaster: 'pusher',
    key: '123',
    cluster: 'mt1',
    wsHost: '192.168.0.155',
    wsPort: 6001,
    forceTLS: false,
    disableStatus: true,
  });;
  
  ngOnInit(): void {
    localStorage.removeItem('positions');
    
    this.websocket();

    // this.js.getQueue().subscribe(
    //   (response) => {
    //     console.log(response)
    //   }
    // )

    // Inicia la búsqueda de partida cuando el componente se inicie
    this.js.buscarPartida().subscribe(
      (response) => {
        console.log(response)
      }, (error) => {
        console.log("ERROR",error)
      }
    )
    

  }

  cancelar(){
    this.js.cancelarPartida().subscribe(
      (response) => {
        console.log(response)
        this.router.navigate(['/dashboard'])
      }
    )
  }

  websocket() {
    this.echo.channel('matchplayer').listen('.MatchPlayers', (res: any) => {
      if(res){
        this.router.navigate(['/juego'])
      }
    });

    console.log(this.echo)
    this.echo.connector.socketId((socketId: string) => {
      console.log(socketId)
    })
    
  }

  ngOnDestroy(): void {
    this.js.cancelarPartida().subscribe(
      (response) => {
        console.log(response)
      }
    )
  }

}
