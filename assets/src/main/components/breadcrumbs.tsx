import {observer} from "mobx-react-lite";
import React from "react";

export interface BreadcrumbsItem {
  isLinkExists: boolean
  linkUrl: null | string
  itemName: string
}

interface Props {
  items: BreadcrumbsItem[]
}

export const Breadcrumbs = observer((props: Props) => {

  return (
    <ul className="breadcrumbs">
      {props.items.map((breadcrumb: BreadcrumbsItem, index: number) => {
        if (breadcrumb.isLinkExists && null !== breadcrumb.linkUrl) {
          return (
            <li key={index}>
              <a href={breadcrumb.linkUrl} itemProp="url">
                <span itemProp="title">{breadcrumb.itemName}</span>
              </a>
            </li>
          )
        } else {
          return (
            <li key={index}>
              <span
                itemProp="title">{breadcrumb.itemName}</span>
            </li>
          )
        }
      })}
    </ul>
  )
})
