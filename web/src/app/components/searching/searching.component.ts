import { Component, OnInit } from '@angular/core';
import { SpinnerComponent } from '../spinner/spinner.component';
import { Router } from '@angular/router';

@Component({
  selector: 'app-searching',
  standalone: true,
  imports: [SpinnerComponent],
  templateUrl: './searching.component.html',
  styleUrl: './searching.component.css'
})
export class SearchingComponent implements OnInit {

  constructor(private router: Router) { }

  ngOnInit(): void {
    setTimeout(() => {
      this.router.navigate(['/juego'])
    }, 2000)
  }

}
