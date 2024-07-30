// offers.module.ts
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { NgbTooltipModule } from '@ng-bootstrap/ng-bootstrap';
import { OffersRoutingModule } from './offers-routing.module';
import { OffersComponent } from './offers.component'; 
import { OmraComponent } from './omra/omra.component';
import { TripsComponent } from './trips/trips.component';
import { HikingComponent } from './hiking/hiking.component';
import { ExursionsPageComponent } from './exursions-page/exursions-page.component';
import { ExursionsModule } from 'src/app/modules/exursions/exursions.module';
import { WidgetsModule } from "../../_metronic/partials/content/widgets/widgets.module";

@NgModule({
  declarations: [
    OffersComponent,
    OmraComponent,
    TripsComponent,
    HikingComponent,
    ExursionsPageComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    NgbTooltipModule,
    OffersRoutingModule,
    ExursionsModule, 
    WidgetsModule
  ],
})
export class OffersModule {}
