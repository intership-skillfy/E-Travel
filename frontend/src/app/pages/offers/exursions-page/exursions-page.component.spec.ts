import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExursionsPageComponent } from './exursions-page.component';

describe('ExursionsPageComponent', () => {
  let component: ExursionsPageComponent;
  let fixture: ComponentFixture<ExursionsPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExursionsPageComponent]
    });
    fixture = TestBed.createComponent(ExursionsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
